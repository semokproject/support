<?php

namespace Semok\Support\Middleware;

use Closure;
use Config;
use \Illuminate\Http\Request;

class DomainFilter
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('host');
        $domain = str_replace('www.','',$host);
        Config::set('semok.middleware.domainfilter.domain', $domain);

        $domainFilter = config('semok.middleware.domainfilter', false);
        if (!$domainFilter) return $next($request);

        $redirectDomain = config('semok.middleware.domainfilter.redirect_domain', false);
        if (is_array($redirectDomain) && isset($redirectDomain[$domain])) {
            if ($redirectDomain[$domain] == 'www' && substr($host, 0, 4) != 'www.') {
                $request->headers->set('host', 'www.' . $domain);
                return redirect($request->path());
            } elseif($redirectDomain[$domain] == 'non_www' && substr($host, 0, 4) == 'www.') {
                $request->headers->set('host', $domain);
                return redirect($request->path());
            } elseif(isset($redirectDomain[$domain]['type']) && $redirectDomain[$domain]['type'] == 'page') {
                return redirect($redirectDomain[$domain]['page']);
            } elseif(isset($redirectDomain[$domain]['type']) && $redirectDomain[$domain]['type'] == 'domain') {
                $request->headers->set('host', $redirectDomain[$domain]['domain']);
                return redirect($request->path());
            }
        }

        $redirectUrl = config('semok.middleware.domainfilter.redirect_url', false);
        if (is_array($redirectUrl) && !empty($redirectUrl)) {
            $urlToRedirect = array_pluck($redirectUrl, 'from');
            $currentUrl = url()->current();
            if (!empty($urlToRedirect) && in_array($currentUrl, $urlToRedirect)) {
                $fullCurrentUrl = url()->full();
                $urlRedirection = array_first($redirectUrl, function($value, $key) use ($currentUrl) {
                    return $value['from'] == $currentUrl;
                });
                $targetUrl = str_replace($urlRedirection['from'], $urlRedirection['to'], $fullCurrentUrl);
                return redirect($targetUrl, $urlRedirection['type']);
            }
        }
        return $next($request);
    }
}
