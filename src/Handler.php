<?php

namespace Wcactus\CroppedImages;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * The main part of module logic
 */
class Handler
{
	protected $mimeToEncodeFormat = [
		'image/jpeg' => 'jpg',
		'image/pjpeg' => 'jpg',
		'image/png' => 'png',
		'image/gif' => 'gif',
		'image/tiff' => 'tif',
		'image/bmp' => 'bmp',
		'image/x-icon' => 'ico',
		'image/vnd.microsoft.icon' => 'ico'
	];

    /**
     * @var Illuminate\Filesystem\FilesystemAdapter
     */
	protected $disk;
	
    /**
     * @var string
     */
	protected $folder;

    /**
     * Constructor
     */
	function __construct() {
		$this->disk = Storage::disk(config('croppedimages.disk'));
		$this->folder = config('croppedimages.folder');
	}
	
    /**
     * Register route to get existing images and their crops and to crop images
     * on the fly.
     * The image URL looks like
     * http(s)://app.url/.../uplinkType/cropName/uplinkId/filename
     */
	public function routes() {
		$prefix = $this->disk->url($this->folder);
		if (strpos($prefix, config('app.url')) === 0)
			$prefix = substr_replace($prefix, '', 0, strlen(config('app.url')));

		Route::get($prefix . '/{uplinkType}/{cropName}/{uplinkId}/{filename}', function($uplinkType, $cropName, $uplinkId, $filename) {
			$uplinkType = str_replace('-', '\\', $uplinkType);
			
			$image = CroppedImage::where([
					['uplink_type', '=', $uplinkType],
					['uplink_id', '=', $uplinkId],
					['filename', '=', $filename],
				])->firstOrFail();
			
			try {
				$path = $this->createCrop($image, $cropName);
			} catch (CroppedImagesException $e) {
				if (env('APP_DEBUG') == false) {
					abort(404);
				} else {
					throw $e;
				}
			}
			
			return response()->file($path);
		})->name('croppedimage');
	}

    /**
     * Get image URL.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
	 * @param string $cropName Crop name.
     * @return string
     */
	public function src($image, $cropName) {
		return route('croppedimage', [
			'uplinkType' => str_replace('\\', '-', $image->uplink_type),
			'cropName' => $cropName,
			'uplinkId' => $image->uplink_id,
			'filename' => urlencode($image->filename)
		]);
	}

    /**
     * Return confugured crops for provided uplink's class name.
     *
     * @param string $uplinkClass Class name of CroppedImage's uplink.
     * @return array
     */
	public function configuredCrops($uplinkClass) {
		if (!array_key_exists($uplinkClass, config('croppedimages.crops')))
			throw new CroppedImagesException('This crop was not configured: ' . $uplinkClass);
		return config('croppedimages.crops')[$uplinkClass];
	}

    /**
     * Save new image.
     * Only the original image is saved, no crops are created here.
     *
     * @param Illuminate\Database\Eloquent\Model $uplink The Eloquent model to which the image is attached.
	 * @param string $sourcePath Path to file.
	 * @param string $alt Image description (optional, for "alt" attribute, for example).
	 * @param string $filename Name with which the file will be saved. Optional, will be taken from sourcePath if was not provided.
	 * @param string $hash MD5-hash of file. Optional, will be calculated if was not provided explicitly.
     * @return string
     */
	public function addImage(&$uplink, $sourcePath, $alt=null, $filename=null, $hash=null) {
		if (!$filename) $filename = basename($sourcePath);
		
		$path = $this->folder . '/' . str_replace('\\', '-', get_class($uplink)). '/original/' . $uplink->id . '/' . $filename;
		$dirPath = $this->folder . '/' . str_replace('\\', '-', get_class($uplink)). '/original/' . $uplink->id;
		
		if ($this->disk->exists($path)) {
			$pathParts = pathinfo($filename);
			for ($i = 1; $i <= 100; $i++) {
				$testFilename = $pathParts['filename'] . '_' . $i . '.' . $pathParts['extension'];
				$testPath = $dirPath . '/' . $testFilename;
				if (!$this->disk->exists($testPath)) {
					$filename = $testFilename;
					$path = $testPath;
					break;
				}
			}
		}
		
		$this->disk->put($path, file_get_contents($sourcePath));
		
		if (!$hash) $hash = md5_file($sourcePath);
		
		$img = Image::make($this->disk->get($path));
		
		$image = new CroppedImage;
		$image->filename = $filename;
		$image->alt = $alt;
		$image->mime = $img->mime();
		$image->orig_w = $img->width();
		$image->orig_h = $img->height();
		$image->hash = $hash;
		$image->position = $uplink->images()->count() + 1;
		
		return $uplink->images()->save($image);
	}

    /**
     * Move image one position up.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     */
	public function moveUp(&$image) {
		if ($image->position > 1) {
			CroppedImage::where('uplink_type', $image->uplink_type)
				->where('uplink_id', $image->uplink_id)
				->where('position', '>=', $image->position - 1)
				->update(['position' => DB::raw('position + 1')]);
				
			$image->position--;
			$image->save();
			
			$this->reorder($image->uplink_type, $image->uplink_id);
		}
	}

    /**
     * Move image one position down.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     */
	public function moveDown(&$image) {
		CroppedImage::where('uplink_type', $image->uplink_type)
			->where('uplink_id', $image->uplink_id)
			->where('position', '>', $image->position + 1)
			->update(['position' => DB::raw('position + 1')]);
			
		$image->position += 2;
		$image->save();
		
		$this->reorder($image->uplink_type, $image->uplink_id);
	}

    /**
     * Delete original image and all its crops.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     */
	public function removeImage(&$image) {
		foreach ($image->crops() as $crop)
			$this->deleteFile($this->folder . '/' . str_replace('\\', '-', $image->uplink_type). '/' . $crop->name . '/' . $image->uplink_id, $image->filename);
		
		$this->deleteFile($this->folder . '/' . str_replace('\\', '-', $image->uplink_type). '/original/' . $image->uplink_id, $image->filename);
		
		$image->delete(); // and crops, cascadely
		
		$this->reorder($image->uplink_type, $image->uplink_id);
	}

    /**
     * Delete all images and their crops for provided uplink model.
     *
     * @param Illuminate\Database\Eloquent\Model $uplink The Eloquent model to which the image is attached.
     */
	public function removeAllImages(&$uplink) {
		$uplinkType = get_class($uplink);
		if (!array_key_exists($uplinkType, config('croppedimages.crops'))) return;

		$cropConfigs = config('croppedimages.crops')[$uplinkType];
		$pathPrefix = $this->folder . '/' . str_replace('\\', '-', $uplinkType). '/';

		$images = $uplink->images();		
		foreach ($images as $image) {
			foreach ($cropConfigs as $cropName=>$cropConfig)
				$this->deleteFile($pathPrefix . $cropName . '/' . $image->uplink_id, $image->filename);
			
			$path = $pathPrefix . '/original/' . $image->uplink_id . '/' . $image->filename;
			$this->deleteFile($pathPrefix . '/original/' . $image->uplink_id, $image->filename);
		}
		
		$images->delete(); // and crops, cascadely
	}                      

    /**
     * Create all configured crops of image.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     */
	public function createAllCrops(&$image) {
		$uplinkType = $image->uplink_type;
		if (!array_key_exists($uplinkType, config('croppedimages.crops'))) return;
		$cropConfigs = config('croppedimages.crops')[$uplinkType];
		foreach ($cropConfigs as $cropName=>$cropConfig)
			$this->createCrop($image, $cropName);
	}                      

    /**
     * Create one crop of image and return crop file's path.
     * If only the name of the crop is specified, it will be created in automatic
     * mode. But it's possible to explicitly set crop parameters, such as offsets
     * and scale.
     * The asteriskW and asteriskH parameters taking part if crop with non-fixed
     * (i.e. '*') width or height is created with explicitly set parameters.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     * @param string $cropName Crop name.
     * @param float $x Horizontal offset.
	 * @param float $y Vertical offset.
	 * @param float $scale Scale.
	 * @param integer $asteriskW Width of the crop (for crops configured with non-fixed ('*') width).
	 * @param integer $asteriskH Height of the crop (for crops configured with non-fixed ('*') height).
     * @return string
     */
	public function createCrop(&$image, $cropName, $x=null, $y=null, $scale=null, $asteriskW=null, $asteriskH=null) {
		$uplinkType = $image->uplink_type;
		
		if (!array_key_exists($uplinkType, config('croppedimages.crops')))
			throw new CroppedImagesException('This uplink type has no crops: ' . $uplinkType);
		$cropConfigs = config('croppedimages.crops')[$uplinkType];
		
		if (!array_key_exists($cropName, $cropConfigs))
			throw new CroppedImagesException('This crop was not configured: ' . $cropName);
		$cropConfig = $cropConfigs[$cropName];

		$pathPrefix = $this->folder . '/' . str_replace('\\', '-', $uplinkType);
		
		$sourcePath = $pathPrefix . '/original/' . $image->uplink_id . '/' . $image->filename;
		$targetPath = $pathPrefix . '/' . $cropName . '/' . $image->uplink_id . '/' . $image->filename;
		$targetDirPath = $pathPrefix . '/' . $cropName . '/' . $image->uplink_id;
		
		if (!$this->disk->exists($sourcePath))
			throw new CroppedImagesException('Original image file not found: ' . $sourcePath);
		
		$img = Image::make($this->disk->get($sourcePath));

		$cropper = new Cropper;
		if (!is_null($x) && !is_null($y) && !is_null($scale)) {
			$croppedImg = $cropper->exact($img, $cropConfig, $x, $y, $scale, $asteriskW, $asteriskH);
		} else {
			if ($cropConfig['method'] == 'crop') {
				$croppedImg = $cropper->crop($img, $cropConfig);
			} else {
				$croppedImg = $cropper->fit($img, $cropConfig);
			}
		}
		
		$this->disk->put($targetPath, (string) $croppedImg->encode($this->mimeToEncodeFormat[$img->mime()]));
		
		$crop = CroppedImageCrop::where([
				['cropped_image_id', '=', $image->id],
				['name', '=', $cropName],
			])->first();
		if (!$crop) {
			$crop = new CroppedImageCrop;
			$crop->cropped_image_id = $image->id;
			$crop->name = $cropName;
		}
		$crop->fill($cropper->lastProps());
		$crop->w = $asteriskW;
		$crop->h = $asteriskH;

		$crop->save();
		
		return $this->disk->path($targetPath);
	}                      

    /**
     * Delete one crop of image.
     *
     * @param Wcactus\CroppedImages\CroppedImage $image CroppedImage model.
     * @param string $cropName Crop name.
     */
	public function removeCrop(&$image, $cropName) {
		$crop = CroppedImageCrop::where([
				['cropped_image_id', '=', $image->id],
				['name', '=', $cropName],
			])->first();
		if ($crop) {
			$pathPrefix = $this->folder . '/' . str_replace('\\', '-', $image->uplink_type). '/';
			$this->deleteFile($pathPrefix . $cropName . '/' . $image->uplink_id, $image->filename);
			$crop->delete();
		}
	}

    /**
     * Delete file on storage disk.
     *
     * @param string $dir
     * @param string $filename
     */
	protected function deleteFile($dir, $filename) {
		$path = $dir . '/' . $filename;
		if ($this->disk->exists($path)) {
			$this->disk->delete($path);
			if (empty($this->disk->allFiles($dir)))	$this->disk->deleteDirectory($dir);
		}
	}
	
    /**
     * Reorder images within the uplink.
     *
     * @param string $uplink_type
     * @param integer $uplink_id
     */
	protected function reorder($uplink_type, $uplink_id) {
		if (config('database.default') == 'mysql') {
			DB::statement('SELECT @position := 0');
			DB::update('UPDATE `croppedimages` set `position` = (@position := @position + 1) where `uplink_type`=? and `uplink_id`=? order by `position`, `id`', [$uplink_type, $uplink_id]);
		} else {
			$images = CroppedImage::where([
					['uplink_type', '=', $uplink_type],
					['uplink_id', '=', $uplink_id],
				])
				->orderBy('position')
				->get();
				
			$pos = 1;
			foreach($images as $image) {
				$image->position = $pos++;
				$image->save();
			}
		}
	}
}