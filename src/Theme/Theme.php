<?php

namespace Semok\Support\Theme;

use File;
use SemokLog;
use SemokTheme;
use Semok\Support\Exceptions\RuntimeException;
use Semok\Support\Theme\Exceptions\ThemeException;

class Theme {

    public $name;
    public $viewsPath;
    public $parent;
    public $settings = [];

    public function __construct($themeName, $viewsPath = null, Theme $parent = null)
    {
        $this->name = $themeName;
        $this->viewsPath = $viewsPath === null ? $themeName : $viewsPath;
        $this->parent = $parent;
        SemokTheme::add($this);
   }

   public function getViewPaths()
   {
        // Build Paths array.
        // All paths are relative to Config::get('theme.theme_path')
        $paths = [];
        $theme = $this;
        do {
            if (substr($theme->viewsPath, 0, 1) === DIRECTORY_SEPARATOR) {
                $path = base_path(substr($theme->viewsPath, 1));
            } else {
                $path = semok_themes_path($theme->viewsPath);
            }
            if (!in_array($path, $paths)) {
                $paths[] = $path;
            }
        } while ($theme = $theme->parent);
        return $paths;
   }

    public function url($url)
    {
        $url = ltrim($url, '/');
        // return external URLs unmodified
        if (preg_match('/^((http(s?):)?\/\/)/i',$url)) {
            return $url;
        }

        // Check for valid {xxx} keys and replace them with the Theme's configuration value (in themes.php)
        preg_match_all('/\{(.*?)\}/', $url, $matches);
        foreach ($matches[1] as $param) {
            if (($value=$this->getSetting($param)) !== null) {
                $url = str_replace('{'.$param.'}', $value, $url);
            }
        }

        // Seperate url from url queries
        if (($position = strpos($url, '?')) !== false) {
            $baseUrl = substr($url, 0, $position);
            $params = substr($url, $position);
        } else {
            $baseUrl = $url;
            $params = '';
        }

        // Lookup asset in current's theme asset path
        $fullUrl = $this->viewsPath . '/' . $baseUrl;
        if (file_exists($fullPath = semok_themes_path($fullUrl))) {
            return semok_themes_url($fullUrl) . $params;
        }

        // If not found then lookup in parent's theme asset path
        if ($parentTheme = $this->getParent()) {
            return $parentTheme->url($url);
        } else { // No parent theme? Lookup in the public folder.
            if (file_exists(public_path($baseUrl))){
                return "/".$baseUrl.$params;
            }
        }

        // Asset not found at all. Error handling
        $action = config('semok.themes.asset_not_found','LOG_ERROR');

        if ($action == 'THROW_EXCEPTION') {
            throw new ThemeException("Asset not found [$url]");
        } elseif($action == 'LOG_ERROR') {
            SemokLog::file('semok')->warning("Asset not found [$url] in Theme [" . SemokTheme::current()->name . "]");
        } else{ // themes.asset_not_found = 'IGNORE'
            return '/'.$url;
        }
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Theme $parent)
    {
        $this->parent = $parent;
    }


    public function install($clearPaths = false)
    {
        $viewsPath = semok_themes_path($this->viewsPath);

        if ($clearPaths) {
            if (File::exists($viewsPath)) {
                File::deleteDirectory($viewsPath);
            }
        }

        File::makeDirectory($viewsPath);

        $themeJson = new Manifest(array_merge($this->settings, [
            'name'          => $this->name,
            'extends'       => $this->parent ? $this->parent->name : null,
        ]));
        $themeJson->saveToFile("$viewsPath/theme.json");

        SemokTheme::rebuildCache();
    }


    public function uninstall()
    {
        $viewsPath = semok_themes_path($this->viewsPath);

        // Calculate absolute paths
        $viewsPath = semok_themes_path($this->viewsPath);

        // Check that paths exist
        $viewsExists = File::exists($viewsPath);

        // Check that no other theme uses to the same paths (ie a child theme)
        foreach (SemokTheme::all() as $t) {
            if ($t !== $this && $viewsExists && $t->viewsPath == $this->viewsPath) {
                throw new RuntimeException("Can not delete folder [$viewsPath] of theme [{$this->name}] because it is also used by theme [{$t->name}]", 1);
            }
        }

        File::deleteDirectory($viewsPath);

        SemokTheme::rebuildCache();
    }

    /*--------------------------------------------------------------------------
    | Theme Settings
    |--------------------------------------------------------------------------*/

    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSetting($key, $default = null)
    {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        } elseif($parent = $this->getParent()) {
            return $parent->getSetting($key,$default);
        } else {
            return $default;
        }
    }

    public function loadSettings($settings = [])
    {
        $this->settings= array_diff_key((array) $settings, array_flip([
            'name',
            'extends',
            'views-path',
        ]));
    }
}
