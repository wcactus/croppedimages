<?php

namespace Wcactus\CroppedImages;

use Illuminate\Database\Eloquent\Model;

class CroppedImageCrop extends Model
{
	public $timestamps = false;

	protected $table = 'croppedimages_crops';
	protected $fillable = ['x', 'y', 'scale', 'w', 'h'];
	
	public function image()
	{
		return $this->belongsTo('Wcactus\CroppedImages\CroppedImage');
	}
}
