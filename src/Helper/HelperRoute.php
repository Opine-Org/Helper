<?php
namespace Helper;
use Handlebars\Handlebars;

class HelperRoute {
	public static function helpers () {
		$cacheFile = $_SERVER['DOCUMENT_ROOT'] . '/helpers/cache.json';
		if (!file_exists($cacheFile)) {
			return;
		}
		$helpers = (array)json_decode(file_get_contents($cacheFile), true);
		if (!is_array($helpers)) {
			return;
		}
		$handlebars = Handlebars::factory();
		foreach ($helpers as $helper) {
			$filename = $_SERVER['DOCUMENT_ROOT'] . '/helpers/' . $helper . '.php';
			if (!file_exists($filename)) {
				continue;
			}
			$callback = require $filename;
			$handlebars->addHelper($helper, $callback);
		}
	}

	public static function build ($root) {
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