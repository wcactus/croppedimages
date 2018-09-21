<?php

namespace Wcactus\CroppedImages;

use Illuminate\Database\Eloquent\Model;

class CroppedImage extends Model
{
	protected $table = 'croppedimages';
	protected $fillable = ['alt'];
	
	public function uplink()
	{
		return $this->morphTo();
	}
	
	public function crops()
	{
		return $this->hasMany('Wcactus\CroppedImages\CroppedImageCrop');
	}
}
