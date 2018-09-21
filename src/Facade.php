<?php

namespace Wcactus\CroppedImages;

class Facade extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'Wcactus\CroppedImages\Handler';
    }
}