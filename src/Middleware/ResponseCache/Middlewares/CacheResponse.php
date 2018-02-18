<?php

namespace Semok\Support\Middleware\ResponseCache\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Semok\Support\Middleware\ResponseCache\ResponseCache;
use Semok\Support\Middleware\ResponseCache\Events\CacheMissed;
use Symfony\Component\HttpFoundation\Response;
use Semok\Support\Middleware\ResponseCache\Events\ResponseCacheHit;

class CacheResponse
{
    /** @var \Semok\Support\Middleware\ResponseCache\ResponseCache */
    protected $responseCache;

    public function __construct(ResponseCache $responseCache)
    {
        $this->responseCache = $responseCache;
    }

    public function handle(Request $request, Closure $next, $lifetimeInMinutes = null): Response
    {
        if ($this->responseCache->enabled($request)) {
            if ($this->responseCache->hasBeenCached($request)) {
                return $this->responseCache->getCachedResponseFor($request);
            }
        }

        $response = $next($request);

        if ($this->responseCache->enabled($request)) {
            if ($this->responseCache->shouldCache($request, $response)) {
                $this->responseCache->cacheResponse($request, $response, $lifetimeInMinutes);
            }
        }
        return $response;
    }
}
