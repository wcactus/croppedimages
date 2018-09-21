<?php

namespace Wcactus\CroppedImages;

use Intervention\Image\Facades\Image;

class Cropper
{
	protected $x, $y, $scale;
	
    /**
     * Create crop with provided offsets and scale.
     *
     * @param Intervention\Image\Image $img
     * @param array $cropConfig
     * @param float $x
	 * @param float $y
	 * @param float $scale
	 * @param integer $asteriskW
	 * @param integer $asteriskH
     * @return Intervention\Image\Image
     */
	public function exact($img, $cropConfig, $x, $y, $scale, $asteriskW=null, $asteriskH=null) {
		if (($cropConfig['w'] == '*') && ($cropConfig['h'] == '*'))
			throw new CroppedImagesException("Width and heights can't be configured both as non-fixed.");

		$origW = $img->width();
		$origH = $img->height();

		if ($cropConfig['w'] != '*') {
			$cropConfigW = $cropConfig['w'];
		} else {
			$cropConfigW = $asteriskW ? $asteriskW : round($origW * $scale + $x);
			if ($cropConfigW < 1) $cropConfigW = $cropConfig['h'];
		}
		if ($cropConfig['h'] != '*') {
			$cropConfigH = $cropConfig['h'];
		} else {
			$cropConfigH = $asteriskH ? $asteriskH : round($origH * $scale + $y);
			if ($cropConfigH < 1) $cropConfigH = $cropConfig['w'];
		}
		
		$canvas = Image::canvas(round($cropConfigW/$scale), round($cropConfigH/$scale), $cropConfig['bg']);
		$canvas->insert($img, 'top-left', round($x/$scale), round($y/$scale));
		if (($canvas->width() > $cropConfigW) || ($canvas->height() > $cropConfigH))
			$canvas->resize($cropConfigW, $cropConfigH);
		
		$this->x = $x;
		$this->y = $y;
		$this->scale = $scale;
		
		return $canvas;
	}

    /**
     * Create crop using 'crop' method - image will be proportionally resized and
     * placed at the center of crop area, so there are no empty spaces at the
     * edges. Some parts of the image may be trimmed if aspect ratio of the image
     * differs from area's aspect ratio.
     * This method doesn't support '*' for width or height.
     *
     * @param Intervention\Image\Image $img
     * @param array $cropConfig
     * @return Intervention\Image\Image
     */
	public function crop($img, $cropConfig) {
		if (($cropConfig['w'] == '*') || ($cropConfig['h'] == '*'))
			throw new CroppedImagesException("This crop doesn't support * as width or height value.");

		$origW = $img->width();
		$origH = $img->height();

		if ($origW/$origH >= $cropConfig['w']/$cropConfig['h']) {
			$canvasH = max($origH, $cropConfig['h']);
			$canvas = Image::canvas(round($canvasH * $cropConfig['w'] / $cropConfig['h']), $canvasH, $cropConfig['bg']);
		} else {
			$canvasW = max($origW, $cropConfig['w']);
			$canvas = Image::canvas($canvasW, round($canvasW * $cropConfig['h'] / $cropConfig['w']), $cropConfig['bg']);
		}

		$canvas->insert($img, 'center');
		$canvas->fit($cropConfig['w'], $cropConfig['h']);
		
		if ($origW/$origH >= $cropConfig['w']/$cropConfig['h']) {
			if ($origH >= $cropConfig['h']) {
				$this->scale = $origH > $canvas->height() ? $canvas->height() / $origH : 1;
				$this->y = 0;
			} else {
				$this->scale = 1;
				$this->y = ($cropConfig['h'] - $origH) / 2;
			}
			$this->x = ($cropConfig['w'] - $origW * $this->scale) / 2;
		} else {
			if ($origW >= $cropConfig['w']) {
				$this->scale = $cropConfig['w'] / $origW;
				$this->x = 0;
			} else {
				$this->scale = 1;
				$this->x = ($cropConfig['w'] - $origW) / 2;
			}
			$this->y = ($cropConfig['h'] - $origH * $this->scale) / 2;
		}
		
		return $canvas;
	}

    /**
     * Create crop using 'crop' method - image will be proportionally resized and
     * placed at the center of crop area, so the image is fully fit in the area.
     * There can be empty spaces at the edges of area if aspect ratio of the
     * image differs from area's aspect ratio.
     * This method supports '*' for width or height.
     *
     * @param Intervention\Image\Image $img
     * @param array $cropConfig
     * @return Intervention\Image\Image
     */
	public function fit($img, $cropConfig) {
		if (($cropConfig['w'] == '*') && ($cropConfig['h'] == '*'))
			throw new CroppedImagesException("Width and heights can't be configured both as non-fixed.");

		$origW = $img->width();
		$origH = $img->height();

		if ($cropConfig['w'] == '*') {
			$cropConfigW = ($origH > $cropConfig['h']) ? round($origW * $cropConfig['h'] / $origH) : $origW;
			$cropConfigH = $cropConfig['h'];
		} else if ($cropConfig['h'] == '*') {
			$cropConfigW = $cropConfig['w'];
			$cropConfigH = ($origW > $cropConfig['w']) ? round($origH * $cropConfig['w'] / $origW) : $origH;
		} else {
			$cropConfigW = $cropConfig['w'];
			$cropConfigH = $cropConfig['h'];
		}

		if ($origW/$origH >= $cropConfigW/$cropConfigH) {
			$canvasW = max($origW, $cropConfigW);
			$canvas = Image::canvas($canvasW, round($canvasW * $cropConfigH / $cropConfigW), $cropConfig['bg']);
		} else {
			$canvasH = max($origH, $cropConfigH);
			$canvas = Image::canvas(round($canvasH * $cropConfigW / $cropConfigH), $canvasH, $cropConfig['bg']);
		}
		
		$canvas->insert($img, 'center');
		$canvas->resize($cropConfigW, $cropConfigH, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		});
		
		if ($origW/$origH >= $cropConfigW/$cropConfigH) {
			if ($origW >= $cropConfigW) {
				$this->scale = $canvas->width() / $origW;
				$this->x = 0;
			} else {
				$this->scale = 1;
				$this->x = ($cropConfigW - $origW) / 2;
			}
			$this->y = ($cropConfig['h'] != '*') ? ($cropConfigH - $origH * $this->scale) / 2 : 0;
		} else {
			if ($origH >= $cropConfigH) {
				$this->scale = $canvas->height() / $origH;
				$this->y = 0;
			} else {
				$this->scale = 1;
				$this->y = ($cropConfigH - $origH) / 2;
			}
			$this->x = ($cropConfig['w'] != '*') ? ($cropConfigW - $origW * $this->scale) / 2 : 0;
		}
		
		return $canvas;
	}

    /**
     * Return horizontal and vertical offsets and scale of last processed image.
     *
     * @return array
     */
	public function lastProps() {
		return [
			'x' => $this->x,
			'y' => $this->y,
			'scale' => $this->scale
		];
	}
}