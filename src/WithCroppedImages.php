<?php

namespace Wcactus\CroppedImages;

trait WithCroppedImages
{
	public function images() {
		return $this->morphMany('Wcactus\CroppedImages\CroppedImage', 'uplink')->orderBy('position');
	} 
}