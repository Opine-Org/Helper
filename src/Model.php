<?php
/**
 * Opine\Helper\Model
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
namespace Opine\Helper;

class Model {
    private $root;
    private $bundleModel;

    public function __construct ($root, $bundleModel) {
        $this->root = $root;
        $this->bundleModel = $bundleModel;
    }

    public function buildDeprecated ($root, $headers=true) {
        if (!file_exists($root)) {
            return '';
        }
        $tmp = explode('/', $root);
        $type = array_pop($tmp);
        $phpBuffer = '<?php' . "\n" . '$' . $type . ' = [];' . "\n";
        if ($headers === false) {
            $phpBuffer = '';
        }
        $path = $root . '/*.php';
        $helpers = glob($path);
        foreach ($helpers as $helper) {
            $name = trim(basename($helper, '.php'));
            $file = substr(str_replace("\r", '', file_get_contents($helper)), 13);
            $phpBuffer .= '$' . $type . '["' . $name . '"] = ' . $file . "\n\n";
        }
        if ($headers === true) {
            $phpBuffer .= 'return $' . $type . ';' . "\n";
        }
        return $phpBuffer;
    }

    public function build ($root, $headers=true) {
        if (!file_exists($root)) {
            return '';
        }
        $tmp = explode('/', $root);
        $type = array_pop($tmp);
        $phpBuffer = '<?php' . "\n" . '$' . $type . ' = [];' . "\n";
        if ($headers === false) {
            $phpBuffer = '';
        }
        $path = $root . '/*.php';
        $helpers = glob($path);
        foreach ($helpers as $helper) {
            $name = trim(basename($helper, '.php'));
            //$file = substr(str_replace("\r", '', file_get_contents($helper)), 13);
            $phpBuffer .= '$' . $type . '["' . $name . '"] = "\\Opine\\Handlebars\\HelperToService::' . $name . '";' . "\n\n";
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
        $helpers .= $this->build($this->root . '/../vendor/opine/helper/available/helpers', false);
        $hbhelpers .= $this->build($this->root . '/../vendor/opine/helper/available/hbhelpers', false);
        $blockhelpers .= $this->build($this->root . '/../vendor/opine/helper/available/blockhelpers', false);

        //bundled
        $bundles = $this->bundleModel->bundles();
        foreach ($bundles as $bundle) {
            if (!isset($bundle['root'])) {
                continue;
            }
            $helpers .= $this->build($bundle['root'] . '/helpers', false);
            $hbhelpers .= $this->build($bundle['root'] . '/hbhelpers', false);
            $blockhelpers .= $this->build($bundle['root'] . '/blockhelpers', false);
        }

        //project
        $helpers .= $this->build($this->root . '/../public/helpers', false);
        $hbhelpers .= $this->build($this->root . '/../public/hbhelpers', false);
        $blockhelpers .= $this->build($this->root . '/../public/blockhelpers', false);

        //footers
        $helpers .= 'return $helpers;';
        $hbhelpers .= 'return $hbhelpers;';
        $blockhelpers .= 'return $blockhelpers;';

        //write
        $this->writeBuild($this->root . '/../cache/helpers.php', $helpers);
        $this->writeBuild($this->root . '/../cache/hbhelpers.php', $hbhelpers);
        $this->writeBuild($this->root . '/../cache/blockhelpers.php', $blockhelpers);
    }

    private function writeBuild ($path, $data) {
        file_put_contents($path, $data);
    }
}