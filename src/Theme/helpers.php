<?php

if (!function_exists('themes_path')) {

    function themes_path($filename = null)
    {
        return app()->make('semok.themes')->themes_path($filename);
    }
}

if (!function_exists('theme_url')) {

    function theme_url($url)
    {
        return app()->make('semok.themes')->url($url);
    }

}
