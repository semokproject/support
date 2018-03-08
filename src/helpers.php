<?php
if (!function_exists('semok_themes_path')) {
    function semok_themes_path($filename = null) {
        return app()->make('semok.themes')->themes_path($filename);
    }
}

if (!function_exists('semok_themes_url')) {
    function semok_themes_url($filename = null) {
        return app()->make('semok.themes')->themes_url($filename);
    }
}

if (!function_exists('semok_theme_url')) {
    function semok_theme_url($url) {
        return app()->make('semok.themes')->url($url);
    }
}

if (!function_exists('semok_responsecache')) {
	function semok_responsecache($enabled = true) {
		app('config')->set('semok.middleware.responsecache.enabled', $enabled);
	}
}

if (!function_exists('semok_slugify')) {
	function semok_slugify($string, $options = []) {
		$defaultoptions = config('semok.sluggable', []);
		if (is_array($options)) {
            $defaultoptions = array_merge($defaultoptions, $options);
        }
		return app('semok.slugify')->slugify($string, $defaultoptions);
	}
}

if (!function_exists('spp_get_delim')) {
	function spp_get_delim($ref) {
		$search_engines = [
			'google.com'				=> 'q',
			'go.google.com'				=> 'q',
			'maps.google.com'			=> 'q',
			'local.google.com'			=> 'q',
			'search.yahoo.com'			=> 'p',
			'search.msn.com'			=> 'q',
			'bing.com'					=> 'q',
			'msxml.excite.com'			=> 'qkw',
			'search.lycos.com'			=> 'query',
			'alltheweb.com'				=> 'q',
			'search.aol.com'			=> 'query',
			'search.iwon.com'			=> 'searchfor',
			'ask.com'					=> 'q',
			'ask.co.uk'					=> 'ask',
			'search.cometsystems.com'	=> 'qry',
			'hotbot.com'				=> 'query',
			'overture.com'				=> 'Keywords',
			'metacrawler.com'			=> 'qkw',
			'search.netscape.com'		=> 'query',
			'looksmart.com'				=> 'key',
			'dpxml.webcrawler.com'		=> 'qkw',
			'search.earthlink.net'		=> 'q',
			'search.viewpoint.com'		=> 'k',
			'mamma.com'					=> 'query'
		];
		$delim = false;
		if (isset($search_engines[$ref])) {
			$delim = $search_engines[$ref];
		}else{
			$sub13 = substr($ref, 0, 13);
			if(substr($ref, 0, 7) == 'google.')
				$delim = "q";
			elseif($sub13 == 'search.atomz.')
				$delim = "sp-q";
			elseif(substr($ref, 0, 11) == 'search.msn.')
				$delim = "q";
			elseif($sub13 == 'search.yahoo.')
				$delim = "p";
			elseif(preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $ref))
				$delim = "bellsouth";
		}
		return $delim;
	}
}

if (!function_exists('spp_get_refer')) {
	function spp_get_refer() {
		$http_referer = request()->server('HTTP_REFERER');
		if (!$http_referer) return false;
		$referer_info = parse_url($http_referer);
		if (!isset($referer_info['host'])) return false;
		$referer = $referer_info['host'];
		if(substr($referer, 0, 4) == 'www.')
			$referer = substr($referer, 4);
		return $referer;
	}
}

if (!function_exists('spp_get_terms')) {
	function spp_get_terms($d) {
		$terms       = null;
		$query_array = array();
		$query_terms = null;
		$query = @explode($d.'=', request()->server('HTTP_REFERER'));
		$query = @explode('&', $query[1]);
		$query = @urldecode($query[0]);
		$query = str_replace("'", '', $query);
		$query = str_replace('"', '', $query);
		$query_array = preg_split('/[\s,\+\.]+/',$query);
		$query_terms = implode(' ', $query_array);
		$terms = htmlspecialchars(urldecode(trim($query_terms)));
		return $terms;
	}
}

if (!function_exists('spp_setinfo')) {
	function spp_setinfo() {
		$referer = spp_get_refer();
		if (!$referer) return false;
		$delimiter = spp_get_delim($referer);

		if($delimiter){
			$query = spp_get_terms($delimiter);
			$query = strtolower(trim($query));
			if($query && !empty($query)){
				try{
					$search = App\Query::where('query',$query)->where('type', $referer)->firstOrFail();
				}catch(Exception $e){
					$search = new App\Query(['query' => $query,'type' => $referer]);
				}
				$search->total += 1;
				$search->save();
			}
		}
	}
}

if (!function_exists('is_json')) {
	function is_json($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}
