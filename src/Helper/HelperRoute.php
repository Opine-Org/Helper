<?php
namespace Helper;

class HelperRoute {
	public static $cache = false;
	private $slim;
	private $handlebars;

	public function __construct ($slim, $handlebars) {
		$this->slim = $slim;
		$this->handlebars = $handlebars;
	}

	public function cacheSet ($cache) {
		self::$cache = $cache;
	}

	public function helpers ($root) {
		if (!empty(self::$cache)) {
			$helpers = self::$cache;
		} else {
			$cacheFile =  $root . '/helpers/cache.json';
			if (!file_exists($cacheFile)) {
				return;
			}
			$helpers = (array)json_decode(file_get_contents($cacheFile), true);
		}
		if (!is_array($helpers)) {
			return;
		}
		foreach ($helpers as $helper) {
			$filename = $root  . '/helpers/' . $helper . '.php';
			if (!file_exists($filename)) {
				continue;
			}
			$callback = require $filename;
			$this->handlebars->addHelper($helper, $callback);
		}
	}

	public function build ($root) {
		$cache = [];
		$helpers = glob($root . '/helpers/*.php');
		foreach ($helpers as $helper) {
			$cache[] = basename($helper, '.php');
		}
		$json = json_encode($cache, JSON_PRETTY_PRINT);
		file_put_contents($root . '/helpers/cache.json', $json);

		$helpers = glob($root . '/helpers/*.js');
		$jsCache = '';
		foreach ($helpers as $helper) {
			if (basename($helper) == 'helpers.js') {
				continue;
			}
			$jsCache .= file_get_contents($helper) . "\n\n";
		}
		file_put_contents($root . '/js/helpers.js', $jsCache);
		return $json;
	}
}