<?php

namespace Wcactus\CroppedImages\Test;

use Intervention\Image\Facades\Image;
use Wcactus\CroppedImages\CroppedImagesException;
use Wcactus\CroppedImages\Cropper;
use Wcactus\CroppedImages\Test\TestCase;

class TestCropperCrop extends TestCase {
	
	protected $cropConfig;
	
	protected function setUp() {
		parent::setUp();
		
		$this->cropConfig = [
			'label' => 'test crop',
			'method' => 'crop',
			'w' => 320,
			'h' => 180,
			'bg' => '#0000ff', 
		];
	}
	
	public function testCropperCropTallImage() {
		$img = Image::canvas(400, 300, '#000000');
		$img->rectangle(1, 39, 398, 261, function ($draw) {
			$draw->border(3, '#ff0000');
		});
		
		$cropper = new Cropper;
		$croppedImg = $cropper->crop($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>0, 'y'=>-30, 'scale'=>0.8], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 0, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 4, 'hex'), 'top (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(319, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(315, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 179, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 175, 'hex'), 'bottom (background)');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(0, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(4, 90, 'hex'), 'left (background)');
	}
	
	public function testCropperCropWideImage() {
		$img = Image::canvas(800, 300, '#000000');
		$img->rectangle(134, 1, 666, 298, function ($draw) {
			$draw->border(3, '#ff0000');
		});
		
		$cropper = new Cropper;
		$croppedImg = $cropper->crop($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>-80, 'y'=>0, 'scale'=>0.6], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 0, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 4, 'hex'), 'top (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(319, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(315, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 179, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 175, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#ff0000', $croppedImg->pickColor(0, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(4, 90, 'hex'), 'left (background)');
	}

	public function testCropperCropSmallTallImage() {
		$img = Image::canvas(120, 150, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->crop($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');

		$this->assertEquals(['x'=>100, 'y'=>15, 'scale'=>1], $cropper->lastProps(), 'last crop properties');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 14, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 15, 'hex'), 'top (background)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(220, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(219, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 165, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 164, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 90, 'hex'), 'left (background)');
	}
	
	public function testCropperCropSmallWideImage() {
		$img = Image::canvas(240, 120, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->crop($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>40, 'y'=>30, 'scale'=>1], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 29, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 30, 'hex'), 'top (background)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(280, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(279, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 150, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 149, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#0000ff', $croppedImg->pickColor(39, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(40, 90, 'hex'), 'left (background)');
	}
	
	public function testCropperCropNonFixedWidth() {
		$this->cropConfig['w'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		
		$this->expectException(CroppedImagesException::class);
		$croppedImg = $cropper->crop($img, $this->cropConfig);
	}
	
	public function testCropperCropNonFixedHeight() {
		$this->cropConfig['h'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		
		$this->expectException(CroppedImagesException::class);
		$croppedImg = $cropper->crop($img, $this->cropConfig);
	}
	
	public function testCropperCropNonFixedWidthAndHeight() {
		$this->cropConfig['w'] = '*';
		$this->cropConfig['h'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		
		$this->expectException(CroppedImagesException::class);
		$croppedImg = $cropper->crop($img, $this->cropConfig);
	}
}