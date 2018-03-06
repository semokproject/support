<?php

namespace Semok\Support\Theme;

use SemokTheme;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Semok\Support\Exceptions\RuntimeException;
use Semok\Support\Theme\Exceptions\ThemeNotFound;
use Semok\Support\Theme\Exceptions\ThemeAlreadyExists;

class Themes
{

    protected $themesPath;
    protected $activeTheme = null;
    protected $themes = [];
    protected $laravelViewsPath;
    protected $cachePath;

    public function __construct()
    {
        $this->laravelViewsPath = config('view.paths');
        $this->themesPath = public_path(config('semok.themes.themes_path', 'themes'));
        $this->themesUrl = config('semok.themes.themes_path', 'themes');
        $this->cachePath = base_path('bootstrap/cache/themes.php');
    }

    /**
     * Return $filename path located in themes folder
     *
     * @param  string $filename
     * @return string
     */
    public function themes_path($filename = null)
    {
        return $filename ? $this->themesPath . '/' . $filename : $this->themesPath;
    }

    /**
     * Return $filename path located in themes folder
     *
     * @param  string $filename
     * @return string
     */
    public function themes_url($filename = null)
    {
        $url = $filename ? $this->themesUrl . '/' . $filename : $this->themesUrl;
        return "/".ltrim($url, '/');
    }

    /**
     * Return list of registered themes
     *
     * @return array
     */
    public function all()
    {
        return $this->themes;
    }

    /**
     * Check if @themeName is registered
     *
     * @return bool
     */
    public function exists($themeName)
    {
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Enable $themeName & set view paths
     *
     * @return Theme
     */
    public function set($themeName)
    {
        if ($this->exists($themeName)) {
            $theme = $this->find($themeName);
        } else {
            $theme = new Theme($themeName);
        }

        $this->activeTheme = $theme;

        // Get theme view paths
        $paths = $theme->getViewPaths();

        // fall-back to default paths (set in views.php config file)
        foreach ($this->laravelViewsPath as $path) {
            if (!in_array($path, $paths)) {
                $paths[] = $path;
            }
        }
        Config::set('view.paths', $paths);

        $themeViewFinder = app('view.finder');
        $themeViewFinder->setPaths($paths);

        Event::fire('semok.theme.change', $theme);
        return $theme;
    }

    /**
     * Get current theme
     *
     * @return Theme
     */
    public function current()
    {
        return $this->activeTheme ? $this->activeTheme : null;
    }

    /**
     * Get current theme's name
     *
     * @return string
     */
    public function get()
    {
        return $this->current() ? $this->current()->name : '';
    }

    /**
     * Find a theme by it's name
     *
     * @return Theme
     */
    public function find($themeName)
    {
        // Search for registered themes
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return $theme;
            }
        }

        throw new ThemeNotFound($themeName);
    }

    /**
     * Register a new theme
     *
     * @return Theme
     */
    public function add(Theme $theme)
    {
        if ($this->exists($theme->name)) {
            throw new ThemeAlreadyExists($theme);
        }
        $this->themes[] = $theme;
        return $theme;
    }

    // Original view paths defined in config.view.php
    public function getLaravelViewPaths()
    {
        return $this->laravelViewsPath;
    }

    public function cacheEnabled()
    {
        return config('semok.themes.cache', true);
    }

    // Rebuilds the cache file
    public function rebuildCache()
    {
        $themes = $this->scanJsonFiles();
        // file_put_contents($this->cachePath, json_encode($themes, JSON_PRETTY_PRINT));

        $stub = file_get_contents(__DIR__.'/resources/stubs/cache.stub');
        $contents = str_replace('[CACHE]', var_export($themes, true), $stub);
        file_put_contents($this->cachePath, $contents);
    }

    // Loads themes from the cache
    public function loadCache()
    {
        if (!file_exists($this->cachePath)) {
            $this->rebuildCache();
        }

        // $data = json_decode(file_get_contents($this->cachePath), true);

        $data = include($this->cachePath);

        if ($data === null){
            throw new RuntimeException("Invalid theme cache json file [{$this->cachePath}]");
        }
        return $data;
    }

    // Scans theme folders for theme.json files and returns an array of themes
    public function scanJsonFiles()
    {
        $themes = [];
        foreach (glob($this->themes_path('*'), GLOB_ONLYDIR) as $themeFolder) {
            $themeFolder = realpath($themeFolder);
            if (file_exists($jsonFilename = $themeFolder.'/'.'theme.json')) {

                $folders = explode(DIRECTORY_SEPARATOR, $themeFolder);
                $themeName = end($folders);

                // default theme settings
                $defaults = [
                    'name'          => $themeName,
                    'extends'       => null,
                ];

                // If theme.json is not an empty file parse json values
                $json = file_get_contents($jsonFilename);
                if ($json !== "") {
                    $data = json_decode($json, true);
                    if ($data === null) {
                        throw new RuntimeException("Invalid theme.json file at [$themeFolder]");
                    }
                } else {
                    $data = [];
                }

                // We already know views-path since we have scaned folders.
                // we will overide this setting if exists
                $data['views-path'] = $themeName;

                $themes[] = array_merge($defaults, $data);
            }
        }
        return $themes;
    }

    public function loadThemesJson()
    {
        if ($this->cacheEnabled()) {
            return $this->loadCache();
        } else {
            return $this->scanJsonFiles();
        }
    }

    /**
     * Scan all folders inside the themes path & config/themes.php
     * If a "theme.json" file is found then load it and setup theme
     */
    public function scanThemes()
    {

        $parentThemes = [];

        foreach ($this->loadThemesJson() as $data) {

            // Create theme
            $theme = new Theme($data['name'], $data['views-path']);

            // Has a parent theme? Store parent name to resolve later.
            if ($data['extends']) {
                $parentThemes[$theme->name] = $data['extends'];
            }

            // Load the rest of the values as theme Settings
            $theme->loadSettings($data);
        }

        // All themes are loaded. Now we can assign the parents to the child-themes
        foreach ($parentThemes as $childName => $parentName) {
            $child = $this->find($childName);

            if (SemokTheme::exists($parentName)) {
                $parent = $this->find($parentName);
            } else {
                $parent = new Theme($parentName);
            }

            $child->setParent($parent);
        }
    }

    /*--------------------------------------------------------------------------
    | Proxy to current theme
    |--------------------------------------------------------------------------*/

    // Return url of current theme
    public function url($filename)
    {
        // If no Theme set, return /$filename
        if (!$this->current()) {
            return "/".ltrim($filename, '/');
        }

        return $this->current()->url($filename);
    }

    /**
     * Act as a proxy to the current theme. Map theme's functions to the Themes class. (Decorator Pattern)
     */
    public function __call($method, $args)
    {
        if (($theme = $this->current())) {
            return call_user_func_array(array($theme, $method), $args);
        } else {
            throw new RuntimeException("No theme is set. Can not execute method [$method] in [".self::class."]", 1);
        }
    }
}
