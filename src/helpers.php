<?php
if (!function_exists('themes_path')) {
    function themes_path($filename = null) {
        return app()->make('semok.themes')->themes_path($filename);
    }
}

if (!function_exists('theme_url')) {
    function theme_url($url) {
        return app()->make('semok.themes')->url($url);
    }
}

if (!function_exists('current_theme_url')) {
	function current_theme_url($filename = null) {
		$url = ltrim($filename, '/');
		if (!$filename || empty(trim($url))) {
			return url('themes/' . Theme::get());
		}
		return url('themes/' . Theme::get() .'/' . $url);
	}
}

if (!function_exists('str_limit_words')) {
	function str_limit_words($str, $numword = 0){
		if (!$numword) return $str;
		$arr_str = explode(' ', $str);
		$arr_str = array_slice($arr_str, 0, $numword);
		$hasil = implode(' ', $arr_str);
		return $hasil;
	}
}

if (!function_exists('str_slugify')) {
	function str_slugify($string, $options = [], $limit_words = false) {
		$defaultoptions = ['lowercase' => false, 'rulesets' => ['default']];
		if (is_array($options)) $options = array_merge($defaultoptions, $options);
		else $options = $defaultoptions;
		if ($limit_words) return Slugify::slugify(str_limit_words($string, $limit_words), $options);
		return Slugify::slugify($string, $options);
	}
}

if (!function_exists('slug_to_str')) {
	function slug_to_str($slug) {
		return str_replace(['-','+','.'],' ', $slug);
	}
}

if (!function_exists('build_image64')) {
	function build_image64($url){
		$image = @file_get_contents($url);
		if ($image !== false){
			return 'data:image/jpg;base64,'.base64_encode($image);
		}
		return $url;
	}
}

if (!function_exists('shuffle_with_keys')) {
	function shuffle_with_keys(&$array) {
		$aux = array();
		$keys = array_keys($array);
		shuffle($keys);
		foreach($keys as $key) {
			$aux[$key] = $array[$key];
			unset($array[$key]);
		}
		$array = $aux;
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

if (!function_exists('xml_to_array')) {
	function xml_to_array($xml) {
		function normalizeSimpleXML($obj, &$result) {
			$data = $obj;
			if (is_object($data)) {
				$data = get_object_vars($data);
			}
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$res = null;
					normalizeSimpleXML($value, $res);
					if (($key == '@attributes') && ($key)) {
						$result = $res;
					} else {
						$result[$key] = $res;
					}
				}
			} else {
				$result = $data;
			}
		}
		normalizeSimpleXML(simplexml_load_string($xml), $result);
		return ($result);
	}
}

if (!function_exists('str_cutter')) {
	function str_cutter($content, $start, $end=false) {
		if($content && $start) {
			$r = explode($start, $content);
			if (isset($r[1])) {
				if($end){
					$r = explode($end, $r[1]);
					return $r[0];
				}
				return $r[1];
			}
			return false;
		}elseif($content && $end){
			$r = explode($end, $content);
			return $r[0];
		}
		return false;
	}
}
