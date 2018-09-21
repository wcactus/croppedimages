<?php

namespace Wcactus\CroppedImages\Test;

use Intervention\Image\Facades\Image;
use Wcactus\CroppedImages\CroppedImagesException;
use Wcactus\CroppedImages\Cropper;
use Wcactus\CroppedImages\Test\TestCase;

class TestCropperFit extends TestCase {

	protected $cropConfig;
	
	protected function setUp() {
		parent::setUp();
		
		$this->cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => 320,
			'h' => 180,
			'bg' => '#0000ff', 
		];
	}
	
	public function testCropperFitTallImage() {
		$img = Image::canvas(400, 300, '#000000');
		$img->rectangle(2, 2, 397, 297, function ($draw) {
			$draw->border(4, '#ff0000');
		});

		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>40, 'y'=>0, 'scale'=>0.6], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 0, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 4, 'hex'), 'top (image)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(277, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(273, 90, 'hex'), 'right (image)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(283, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 179, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 175, 'hex'), 'bottom (image)');
        
		$this->assertEquals('#ff0000', $croppedImg->pickColor(40, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(44, 90, 'hex'), 'left (image)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(34, 90, 'hex'), 'left (background)');
	}
	
	public function testCropperFitWideImage() {
		$img = Image::canvas(800, 300, '#000000');
		$img->rectangle(2, 2, 797, 297, function ($draw) {
			$draw->border(4, '#ff0000');
		});
		
		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);

		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>0, 'y'=>30, 'scale'=>0.4], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 30, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 34, 'hex'), 'top (image)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 24, 'hex'), 'top (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(319, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(315, 90, 'hex'), 'right (image)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 149, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 145, 'hex'), 'bottom (image)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(160, 155, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#ff0000', $croppedImg->pickColor(0, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(4, 90, 'hex'), 'left (image)');
	}

	public function testCropperFitSmallTallImage() {
		$img = Image::canvas(120, 150, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);

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
	
	public function testCropperFitSmallWideImage() {
		$img = Image::canvas(240, 120, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);

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
	
	public function testCropperFitNonFixedWidth() {
		$this->cropConfig['w'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		$img->rectangle(2, 2, 397, 297, function ($draw) {
			$draw->border(4, '#ff0000');
		});
		
		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);
		
		$this->assertEquals(240, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>0, 'y'=>0, 'scale'=>0.6], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#ff0000', $croppedImg->pickColor(120, 0, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(120, 4, 'hex'), 'top (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(239, 90, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(235, 90, 'hex'), 'right (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(120, 179, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(120, 175, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#ff0000', $croppedImg->pickColor(0, 90, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(4, 90, 'hex'), 'left (background)');
	}
	
	public function testCropperFitNonFixedHeight() {
		$this->cropConfig['h'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		$img->rectangle(2, 2, 397, 297, function ($draw) {
			$draw->border(4, '#ff0000');
		});
		
		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(240, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>0, 'y'=>0, 'scale'=>0.8], $cropper->lastProps(), 'last crop properties');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 0, 'hex'), 'top (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 4, 'hex'), 'top (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(319, 120, 'hex'), 'right (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(315, 120, 'hex'), 'right (background)');
		
		$this->assertEquals('#ff0000', $croppedImg->pickColor(160, 239, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 235, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#ff0000', $croppedImg->pickColor(0, 120, 'hex'), 'left (rectangle)');
		$this->assertEquals('#000000', $croppedImg->pickColor(4, 120, 'hex'), 'left (background)');
	}
	
	public function testCropperFitNonFixedWidthSmallImage() {
		$this->cropConfig['w'] = '*';
		$img = Image::canvas(200, 100, '#000000');
		
		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);
		
		$this->assertEquals(200, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>0, 'y'=>40, 'scale'=>1], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#000000', $croppedImg->pickColor(100, 40, 'hex'), 'top (rectangle)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(100, 39, 'hex'), 'top (background)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(199, 90, 'hex'), 'right (rectangle)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 139, 'hex'), 'bottom (rectangle)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(100, 140, 'hex'), 'bottom (background)');
        
		$this->assertEquals('#000000', $croppedImg->pickColor(0, 90, 'hex'), 'left (rectangle)');
	}
	
	public function testCropperFitNonFixedHeightSmallImage() {
		$this->cropConfig['h'] = '*';
		$img = Image::canvas(100, 200, '#000000');
		
		$cropper = new Cropper;
		$croppedImg = $cropper->fit($img, $this->cropConfig);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(200, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>110, 'y'=>0, 'scale'=>1], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#000000', $croppedImg->pickColor(160, 0, 'hex'), 'top (rectangle)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(209, 100, 'hex'), 'right (rectangle)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(210, 100, 'hex'), 'right (background)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(160, 199, 'hex'), 'bottom (rectangle)');
        
		$this->assertEquals('#000000', $croppedImg->pickColor(110, 100, 'hex'), 'left (rectangle)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(109, 100, 'hex'), 'left (background)');
	}

	public function testCropperFitNonFixedWidthAndHeight() {
		$this->cropConfig['w'] = '*';
		$this->cropConfig['h'] = '*';
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		
		$this->expectException(CroppedImagesException::class);
		$croppedImg = $cropper->fit($img, $this->cropConfig);
	}
}