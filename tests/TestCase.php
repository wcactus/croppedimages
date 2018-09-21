<?php

namespace Wcactus\CroppedImages\Test;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return lasselehtinen\MyPackage\MyPackageServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [
			\Wcactus\CroppedImages\ServiceProvider::class,
			\Intervention\Image\ImageServiceProvider::class,
		];
    }
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'CroppedImages' => \Wcactus\CroppedImages\Facade::class,
			'Image' => \Intervention\Image\Facades\Image::class,
        ];
    }
}