<?php

namespace Semok\Support\Cache;

use Config;
use Illuminate\Cache\CacheManager;

class Cache
{
    protected $cache, $repository;
    public function __construct(CacheManager $cache)
    {
        Config::set(
            'cache.stores.sm-default',
            [
                'driver' => 'file',
                'path' => storage_path('semok/cache/default'),
            ]
        );
        $this->cache = $cache;
    }

    public function dir($dirname)
    {
        $cache_store = 'sm-' . str_replace(['/', '.'], '-', $dirname);
        Config::set(
            'cache.stores.' . $cache_store,
            [
                'driver' => 'file',
                'path' => storage_path('semok/cache/' . $dirname),
            ]
        );
        $this->repository = $this->cache->store($cache_store);
        return $this->repository;
    }

	public function __call($name, $arguments)
	{
        if (!$this->repository) {
            $this->repository = $this->cache->store('sm-default');
        }

        if (
            $name != 'store' &&
            method_exists($this->repository, $name)
        ) {
            return call_user_func_array([$this->repository, $name], $arguments);
        }
	}
}
