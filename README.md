# Laravel 5 module for creating preconfigured image thumbnails on the fly

### Features

- Preconfigured settings for thumbnail creation variants for images attached to Eloquent models
- The thumbnails (further named «the crops») are created according to the defined configuration on-the-fly, being requested by the specially structured URLs
- Tt’s possible to create a crop with explicitly provided cropping parameters
- All created crops are registered in database, so if you need to manually modify the previously created crop using your visual image editor, you do not have to re-scale and move the image from initial position
- Directory structure makes possible to easy remove crops created later when you need to change the cropping configuration

**N.B.** this module **does not** contain visual image editor!

### Installation

This module requires Laravel 5.4 or higher.

1.	Add CroppedImages to your Laravel project: `composer require wcactus/croppedimages`
2.	Publish config file and migration: `php artisan vendor:publish --tag=croppedimages`
3.	Apply migration: `php artisan migrate`

Facade and service provider will be autoloaded.

### Directories structure

Original images and their crops stored using Laravel file storage functionality. You can choose disk and folder inside the disk to save images to.

The path to file looks like this:
_uplinkClass/cropName/uplinkId/fileName_

For original image _cropName_ will be «original».

This structure allows you to delete all the crops created for the uplink in one action, if you need to change the cropping configuration.

### Configuration

The configuration file is located at _config/croppedimages.php_.

The target of configuration process is to define a list of allowed cropping variants for all Eloquent models that can have attached images, and to set cropping parameters for each cropping variant. You can also define storage disk and folder.

Here is configuration example:

```php
return [
	'disk' => 'public',
	'folder' => 'images',
	'crops' => [
		'App\Brand' => [
			'main_page_logo' => [
				'label' => 'logotype for main page',
				'method' => 'fit',
				'w' => 135,
				'h' => 80,
				'bg' => '#000000',
			],
			'small' => [
				'label' => 'small image',
				'method' => 'fit',
				'w' => 140,
				'h' => 140,
				'bg' => '#ffffff',
			],
		],
		'App\Product' => [
			'small' => [
				'label' => 'small image',
				'method' => 'crop',
				'w' => 140,
				'h' => 140,
				'bg' => '#ffffff',
			],
		],
	],
];
```
As you can see, in this example we have defined two crops («main_page_logo» and «small») for App\Brand model, and one crop («small») for App\Product model.

The crop properties are:

- **label**: display name of crop (to show it in website control panel, for example);
- **method**:
  - **'fit'** – when the crop is automatically created, the original image will be proportionally resized and placed inside the crop area without trimming (but there may be an empty spaces at the edges);
  - **'crop'** – when the crop is automatically created, the original image will be proportionally resized and placed inside the crop area without empty spaces around the edges (so part of the image can be cut off);
- **w**: width of crop, in pixels (can be set to '*' if the 'fit' method was chosen);
- **h**: height of crop, in pixels (can be set to '*' if the 'fit' method was chosen);
- **bg**: color of empty spaces, if they occur as a result of cropping.

The chosen cropping method doesn't take any effect when cropping is performed with explicitly defined parameters (offsets and scale).

FYI: if you prefer «public» storage drive to store image files, don’n forget to create a symbolic link using `php artisan storage:link command`.

### Usage

The URL schema is similar to directory structure:
_http(s)://app.url/.../uplinkType/cropName/uplinkId/filename_

Add this to your routes file:
```php
CroppedImages::routes();
```
Use **CroppedImages::src($image, $cropName)** method to generate original image or crop URL in your Blade templates:
```php
<img src="{{ CroppedImages::src($image, 'small') }}" alt="{{ $image->alt }}"/>
```
For original image, $cropName must be set to «original».

Use **WithCroppedImages** trait it in each Eloquent model that can have attached images:
```php
use \Wcactus\CroppedImages\WithCroppedImages;
```
Use methods described below to add, modify and remove original images and their crops.

### Methods

**CroppedImages::routes()**
Register route to get existing images and their crops and to crop images on the fly.

**CroppedImages::src(Wcactus\CroppedImages\CroppedImage $image, string $cropName)**
Get image URL.

**CroppedImages::configuredCrops(string $uplinkClass)**
Return confugured crops for provided uplink's class name.

**CroppedImages::addImage(Illuminate\Database\Eloquent\Model &$uplink, string $sourcePath, string $alt=null, string $filename=null, string $hash=null)**
Save new image. Only the original image is saved, no crops are created here.

**CroppedImages::moveUp(Wcactus\CroppedImages\CroppedImage &$image)**
Move image one position up.

**CroppedImages::moveDown(Wcactus\CroppedImages\CroppedImage &$image)**
Move image one position down.

**CroppedImages::removeImage(Wcactus\CroppedImages\CroppedImage &$image)**
Delete original image and all its crops.

**CroppedImages::removeAllImages(Illuminate\Database\Eloquent\Model &$uplink)**
Delete all images and their crops for provided uplink model.

**CroppedImages::createAllCrops(Wcactus\CroppedImages\CroppedImage &$image)**
Create all configured crops of image.

**CroppedImages::createCrop(Wcactus\CroppedImages\CroppedImage &$image, string $cropName, float $x=null, float $y=null, float $scale=null, integer $asteriskW=null, integer $asteriskH=null)**
Create one crop of image and return crop file's path.

**CroppedImages::removeCrop(Wcactus\CroppedImages\CroppedImage &$image, string $cropName)**
Delete one crop of image.