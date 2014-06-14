<?php
/**
 * Opine\HelperRoute
 *
 * Copyright (c)2013, 2014 Ryan Mahoney, https://github.com/Opine-Org <ryan@virtuecenter.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Opine;

class HelperRoute {
    private $root;
    private $handlebars;
    private $bundleRoot;

    public function __construct ($root, $handlebars, $bundleRoot) {
        $this->root = $root;
        $this->handlebars = $handlebars;
        $this->bundleRoot = $bundleRoot;
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
        $vendorPath = $root . '/../vendor/opine/helper/available/';
        $this->compile($vendorPath, $phpBuffer, $jsBuffer);
        
        //hanlde bundles
        $bundleCache = $root . '/../bundles/cache.json';
        if (file_exists($bundleCache)) {
            $bundles = (array)json_decode(file_get_contents($bundleCache), true);
            if (is_array($bundles) && count($bundles) > 0) {
                foreach ($bundles as $bundleName => $bundles) {
                    $bundlePath = $root . '/../bundles/' . $bundleName . '/public/helpers/';
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

    public function build2 ($root, $headers=true) {
        if (!file_exists($root)) {
            return '';
        }
        $type = array_pop(explode('/', rtrim($root, '/')));
        $phpBuffer = '<?php' . "\n" . '$' . $type . ' = [];' . "\n"; 
        if ($headers === false) {
            $phpBuffer = '';
        }
        $path = $root . '/*.php';
        $helpers = glob($path);
        foreach ($helpers as $helper) {
            $name = trim(basename($helper, '.php'));
            if (substr($name, -5) == '_build') {
                continue;
            }
            $file = substr(str_replace("\r", '', file_get_contents($helper)), 13);
            $phpBuffer .= '$' . $type . '["' . $name . '"] = ' . $file . "\n\n";
        }
        if ($headers === true) {
            $phpBuffer .= 'return $' . $type . ';' . "\n";
        }
        return $phpBuffer;
    }

    public function buildAll () {
        $helpers = '<?php' . "\n" . '$helpers = [];' . "\n";
        $hbhelpers = '<?php' . "\n" . '$hbhelpers = [];' . "\n";
        $blockhelpers = '<?php' . "\n" . '$blockhelpers = [];' . "\n";

        //universal
        $helpers .= $this->build2($this->root . '/../vendors/opine/helper/available/helpers', false);
        $hbhelpers .= $this->build2($this->root . '/../vendors/opine/helper/available/hbhelpers', false);
        $blockhelpers .= $this->build2($this->root . '/../vendors/opine/helper/available/blockhelpers', false);  
        
        //bundled
        $bundles = $this->bundleRoute->bundles();
        foreach ($bundles as $bundle) {
            $helpers .= $this->build2($this->root . '/../bundles/' . $bundle . '/public/helpers', false);
            $hbhelpers .= $this->build2($this->root . '/../bundles/' . $bundle . '/public/hbhelpers', false);
            $blockhelpers .= $this->build2($this->root . '/../bundles/' . $bundle . '/public/blockhelpers', false);
        }

        //project
        $helpers .= $this->build2($this->root . '/../public/helpers', false);
        $hbhelpers .= $this->build2($this->root . '/../public/hbhelpers', false);
        $blockhelpers .= $this->build2($this->root . '/../public/blockhelpers', false);

        //footers
        $helpers .= 'return $helpers;' . "\n";
        $hbhelpers .= 'return $hbhelpers;' . "\n";
        $blockhelpers .= 'return $blockhelpers;' . "\n";

        //write
        $this->writeBuild($this->root . '/../public/helpers', $helpers);
        $this->writeBuild($this->root . '/../public/hbhelpers', $hbhelpers);
        $this->writeBuild($this->root . '/../public/blockhelpers', $blockhelpers);
    }

    private function writeBuild ($path, $data) {
        if (!file_exists($path)) {
            mkdir($path);
        }
        file_put_contents($path . '/_build.php', $data);        
    }
}