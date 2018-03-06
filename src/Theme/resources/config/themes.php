<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Root path where theme Views will be located.
    | Can be outside default views path e.g.: resources/themes
    | Leave it null if you will put your themes in the default views folder
    | (as defined in config\views.php)
    |--------------------------------------------------------------------------
    */

    'themes_path' => 'themes', // eg: base_path('resources/themes')

	/*
	|--------------------------------------------------------------------------
	| Set behavior if an asset is not found in a Theme hierarchy.
	| Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
	|--------------------------------------------------------------------------
	*/

	'asset_not_found' => 'LOG_ERROR',

	/*
	|--------------------------------------------------------------------------
	| Do we want a theme activated by default? Can be set at runtime with:
	| Theme::set('theme-name');
	|--------------------------------------------------------------------------
	*/

	'default' => null,

	/*
	|--------------------------------------------------------------------------
	| Cache theme.json configuration files that are located in each theme's folder
	| in order to avoid searching theme settings in the filesystem for each request
	|--------------------------------------------------------------------------
	*/

	'cache' => true,

];
