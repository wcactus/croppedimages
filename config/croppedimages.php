<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage and cropping configuration for Wcactus\CroppedImages
    |--------------------------------------------------------------------------
    */

    /**
     * The storage disk used to store original images and their crops.
     *
     * @var string
     */
	'disk' => 'public',

    /**
     * The folder inside the storage disk. All CroppedImages directories
     * hierarchy will be located inside this folder.
     *
     * @var string
     */
	'folder' => 'images',

    /**
     * Here you can define one or more crops for each Eloquent model and set
     * properties of each crop.
     * 
     * The key in array is the name of the crop, use it in all CroppedImages
     * method calls.
     * 
     * The crop properties are:
     *   label: display name of crop (to show it in website control panel,
     *      for example).
     *   method: 'fit' - when the crop is automatically created, the original
     *             image will be proportionally resized and placed inside the
     *             crop area without trimming (but there may be an empty spaces
     *             at the edges).
     *           'crop' - when the crop is automatically created, the original
     *             image will be proportionally resized and placed inside the
     *             crop area without empty spaces around the edges (so part of
     *             the image can be cut off).
     *           The chosen cropping method doesn't take any effect when
     *           cropping is performed with explicitly defined parameters
     *           (offsets and scale).
     *   w: width of crop, in pixels (can be set to '*' if the 'fit' method
     *     was chosen).
     *   h: height of crop, in pixels (can be set to '*' if the 'fit' method
     *     was chosen).
     *   bg: color of empty spaces, if they occur as a result of cropping.
     * 
     * In every Eloquent model what has CroppedImages attached you should
     * use Wcactus\CroppedImages\WithCroppedImages trait.
     *
     * @var array
     */
	'crops' => [
		/*
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
		*/
	],

];
