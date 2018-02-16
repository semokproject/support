<?php

namespace Semok\Support\Middleware\ResponseCache;

use Illuminate\Http\Request;
use Semok\Support\Middleware\ResponseCache\CacheProfiles\CacheProfile;

class RequestHasher
{
    /** @var \Semok\Support\Middleware\ResponseCache\CacheProfiles\CacheProfile */
    protected $cacheProfile;

    public function __construct(CacheProfile $cacheProfile)
    {
        $this->cacheProfile = $cacheProfile;
    }

    public function getHashFor(Request $request): string
    {
        return 'responsecache-'.md5(
            "{$request->getUri()}/{$request->getMethod()}/".$this->cacheProfile->cacheNameSuffix($request)
        );
    }
}
