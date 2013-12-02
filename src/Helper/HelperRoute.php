<?php
namespace Helper;

class HelperRoute {
	private $slim;
	private $handlebars;

	public function __construct ($slim, $handlebars) {
		$this->slim = $slim;
		$this->handlebars = $handlebars;
	}

	public function helpers ($root, $cache=true) {
		require $filename = $root . '/helpers/_build.php';
		foreach ($helpers as $helper => $callback) {
			$this->handlebars->addHelper($helper, $callback);
		}
	}

	public function build ($root) {
		//initialize strings
		$phpBuffer = '<?php' . "\n" . '$helpers = [];' . "\n\n";
		$jsBuffer = '';

		//handle vendors
		$vendorPath = $root . '/../vendor/virtuecenter/helper/available/';
		$this->compile($vendorPath, $phpBuffer, $jsBuffer);
		
		//hanlde bundles
		$bundleCache = $root . '/../bundles/cache.json';
		if (file_exists($bundleCache)) {
			$bundles = (array)json_decode(file_get_contents($bundleCache), true);
			if (is_array($bundles) && count($bundles) > 0) {
				foreach ($bundles as $bundle) {
					$bundlePath = $root . '/../bundles/' . $bundle . '/public/helpers/';
					$this->compile($bundlePath, $phpBuffer, $jsBuffer);
				}
			}
		}

		//handle project
		$projectPath = $root . '/helpers/';  
		$this->compile($projectPath, $phpBuffer, $jsBuffer);

		//write compiled helpers
		$phpCache = $root . '/helpers/_build.php';
		file_put_contents($root . '/js/helpers.js', $jsBuffer);
		file_put_contents($phpCache, $phpBuffer);

		ob_start();
		echo exec('php -l ' . $phpCache);
		$buffer = ob_get_clean();
		if (substr_count($buffer, 'No syntax errors detected in') == 1) {
			echo 'Good: Helper build file is passing PHP error check.', "\n";
		} else {
			echo 'Problem: Helper build file is not passing PHP error check.', "\n";
		}
	}

	private function compile ($root, &$phpBuffer, &$jsBuffer) {
		$path = $root . '*.php';
		$helpers = glob($path);
		foreach ($helpers as $helper) {
			$name = trim(basename($helper, '.php'));
			if ($name == '_build') {
				continue;
			}
			$file = substr(str_replace("\r", '', file_get_contents($helper)), 13);
			$phpBuffer .= '$helpers["' . $name . '"] = ' . $file . "\n\n";
		}
		$path = $root . '*.js';
		$helpers = glob($path);
		foreach ($helpers as $helper) {
			$name = trim(basename($helper, '.js'));
			$jsBuffer .= file_get_contents($helper) . "\n\n";
		}
	}
}