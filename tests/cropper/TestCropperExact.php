<?php

namespace Wcactus\CroppedImages\Test;

use Intervention\Image\Facades\Image;
use Wcactus\CroppedImages\CroppedImagesException;
use Wcactus\CroppedImages\Cropper;
use Wcactus\CroppedImages\Test\TestCase;

class TestCropperExact extends TestCase {

	public function testCropperExactPositiveOffsets() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => 320,
			'h' => 180,
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>100, 'y'=>20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 19, 'hex'), 'top bound (top)');
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 20, 'hex'), 'top bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(299, 95, 'hex'), 'right bound (left)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(300, 95, 'hex'), 'right bound (right)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 169, 'hex'), 'bottom bound (top)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 170, 'hex'), 'bottom bound (bottom)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 95, 'hex'), 'left bound (left)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 95, 'hex'), 'left bound (right)');
	}
	
	public function testCropperExactNegativeOffsets() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => 320,
			'h' => 180,
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');
		
		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, -100, -20, 0.5);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>-100, 'y'=>-20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#000000', $croppedImg->pickColor(50, 0, 'hex'), 'top bound');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(99, 65, 'hex'), 'right bound (left)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(100, 65, 'hex'), 'right bound (right)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(50, 129, 'hex'), 'bottom bound (top)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(50, 130, 'hex'), 'bottom bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(0, 65, 'hex'), 'left bound');
	}

	public function testCropperExactNonFixedWidth() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => '*',
			'h' => 180,
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5);
		
		$this->assertEquals(300, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>100, 'y'=>20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 19, 'hex'), 'top bound (top)');
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 20, 'hex'), 'top bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(299, 95, 'hex'), 'right bound (left)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 169, 'hex'), 'bottom bound (top)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 170, 'hex'), 'bottom bound (bottom)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 95, 'hex'), 'left bound (left)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 95, 'hex'), 'left bound (right)');
	}
	
	public function testCropperExactNonFixedWidthProvided() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => '*',
			'h' => 180,
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5, 320, null);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>100, 'y'=>20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 19, 'hex'), 'top bound (top)');
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 20, 'hex'), 'top bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(299, 95, 'hex'), 'right bound (left)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(300, 95, 'hex'), 'right bound (right)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 169, 'hex'), 'bottom bound (top)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 170, 'hex'), 'bottom bound (bottom)');
		
		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 95, 'hex'), 'left bound (left)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 95, 'hex'), 'left bound (right)');
	}

	public function testCropperExactNonFixedHeight() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => 320,
			'h' => '*',
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(170, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>100, 'y'=>20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 19, 'hex'), 'top bound (top)');
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 20, 'hex'), 'top bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(299, 95, 'hex'), 'right bound (left)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(300, 95, 'hex'), 'right bound (right)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 169, 'hex'), 'bottom bound');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 95, 'hex'), 'left bound (left)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 95, 'hex'), 'left bound (right)');
	}
	
	public function testCropperExactNonFixedHeightProvided() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => 320,
			'h' => '*',
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');

		$cropper = new Cropper;
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5, null, 180);
		
		$this->assertEquals(320, $croppedImg->width(), 'canvas width');
		$this->assertEquals(180, $croppedImg->height(), 'canvas height');
		
		$this->assertEquals(['x'=>100, 'y'=>20, 'scale'=>0.5], $cropper->lastProps(), 'last crop properties');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 19, 'hex'), 'top bound (top)');
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 20, 'hex'), 'top bound (bottom)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(299, 95, 'hex'), 'right bound (left)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(300, 95, 'hex'), 'right bound (right)');
		
		$this->assertEquals('#000000', $croppedImg->pickColor(200, 169, 'hex'), 'bottom bound (top)');
		$this->assertEquals('#0000ff', $croppedImg->pickColor(200, 170, 'hex'), 'bottom bound (bottom)');

		$this->assertEquals('#0000ff', $croppedImg->pickColor(99, 95, 'hex'), 'left bound (left)');
		$this->assertEquals('#000000', $croppedImg->pickColor(100, 95, 'hex'), 'left bound (right)');
	}

	public function testCropperExactNonFixedWidthAndHeight() {
		$cropConfig = [
			'label' => 'test crop',
			'method' => 'fit',
			'w' => '*',
			'h' => '*',
			'bg' => '#0000ff', 
		];
		$img = Image::canvas(400, 300, '#000000');

		$cropper = new Cropper;
		
		$this->expectException(CroppedImagesException::class);
		$croppedImg = $cropper->exact($img, $cropConfig, 100, 20, 0.5, 320, 180);
	}
}