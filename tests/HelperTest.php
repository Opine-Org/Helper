<?php
namespace Opine;

use Exception;
use LightnCandy;
use MongoDate;
use PHPUnit_Framework_TestCase;
use Opine\Config\Service as Config;
use Opine\Container\Service as Container;

class HelperTest extends PHPUnit_Framework_TestCase {
    private $db;
    private $blurbId = 'blurbs:538887dded88e5a5527c1ef6';
    private $helperRoute;

    public function setup () {
        $root = __DIR__ . '/../public';
        $config = new Config($root);
        $config->cacheSet();
        $container = Container::instance($root, $config, $root . '/../container.yml');
        $this->db = $container->get('db');
        $this->ensureDocuments();
        $this->helperRoute = $container->get('helperRoute');
    }

    private function ensureDocuments () {
        $this->db->document($this->blurbId, [
            'title' => 'Test',
            'body' => 'Test Blurb',
            'tags' => ['Test']
        ])->upsert();
    }

    private function compile ($name, $type, $partial, $context) {
        $helpers = [];
        $hbhelpers = [];
        $blockhelpers = [];
        switch ($type) {
            case 'helper':
                $helpers[$name] = $this->helperGet($name);
                break;

            case 'hbhelper':
                $hbhelpers[$name] = $this->hbhelperGet($name);
                break;

            case 'blockhelpers':
                $blockhelpers[$name] = $this->blockhelperGet($name);
                break;

            default:
                throw new Exception('unknown helper type');
        }
        $php = LightnCandy::compile(
            $partial,
            [
                'flags' => LightnCandy::FLAG_ERROR_LOG | LightnCandy::FLAG_STANDALONE | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_SPVARS,
                'helpers' => $helpers,
                'hbhelpers' => $hbhelpers,
                'blockhelpers' => $blockhelpers
            ]
        );
        $tmp = __DIR__ . '/' . uniqid() . '.php';
        file_put_contents($tmp, $php);
        $function = require $tmp;
        unlink($tmp);
        return $function($context);
    }

    private function normalizeResponse ($input) {
        return str_replace(['    ', "\n"], '', $input);
    }

    private function helperGet ($helper) {
        return require __DIR__ . '/../available/helpers/' . $helper . '.php';
    }

    private function hbhelperGet ($helper) {
        return require __DIR__ . '/../available/helpers/' . $helper . '.php';
    }

    private function blockhelperGet ($helper) {
        return require __DIR__ . '/../available/blockhelpers/' . $helper . '.php';
    }

    public function testArrayToCSV () {
        $response = $this->compile(
            'ArrayToCSV',
            'helper',
            '{{{ArrayToCSV test}}}',
            ['test' => ['A', 'B', 'C']]
        );
        $this->assertTrue($response == 'A, B, C');
    }

    public function testAudioJS () {
        $response = $this->compile(
            'AudioJS',
            'helper',
            '{{{AudioJS file}}}',
            ['file' => 'audio.mp3']
        );
        $this->assertTrue($this->normalizeResponse($response) == '<script src="/js/audiojs/audio.min.js"></script><script>audiojs.events.ready(function() {var as = audiojs.createAll();});</script><audio src="audio.mp3" preload="auto" />');
    }

    public function testBlurb () {
        $response = $this->compile(
            'Blurb',
            'helper',
            '{{{Blurb title="Test"}}}',
            []
        );
        $this->assertTrue($response == 'Test Blurb');
    }

    public function testBooleanReadable () {
        $response = $this->compile(
            'BooleanReadable',
            'helper',
            '{{{BooleanReadable test}}}',
            ['test' => 1]
        );
        $this->assertTrue($response === 'Yes');
    }

    public function testCapitalize () {
        $response = $this->compile(
            'Capitalize',
            'helper',
            '{{{Capitalize test}}}',
            ['test' => 'test']
        );
        $this->assertTrue($response === 'Test');
    }

    public function testCategoriesCSV () {
        /*
        $response = $this->compile(
            'CategoriesCSV',
            'helper',
            '{{{CategoriesCSV test}}}',
            ['test' => [$this->categoryId, $this->categoryId2]]
        );
        $this->assertTrue($response === 'Test Category, Test Category Two');
        */
    }

    public function testDisqusComments () {
        /*
        $response = $this->compile(
            'DisqusComments',
            'helper',
            '{{{DisqusComments}}}',
            []
        );
        $this->assertTrue($response === '');
        */
    }

    public function testEachRowConditionalClass () {
        $response = $this->compile(
            'EachRowConditionalClass',
            'helper',
            '{{#each data}}{{@index}}:{{.}}:{{{EachRowConditionalClass @index class="even" otherclass="odd"}}}{{/each}}',
            ['data' => ['a', 'b', 'c', 'd', 'e']]
        );
        $this->assertTrue($response === '0:a:odd1:b:even2:c:odd3:d:even4:e:odd');
    }

    public function testImageResize () {
        $response = $this->compile(
            'ImageResize',
            'helper',
            '{{{ImageResize image=test height="10" width="20"}}}',
            ['test' => ['url' => '/image.jpg', 'width' => 50, 'height' => 100]]
        );
        $this->assertTrue($response === '<img src="/imagecache/20/10/20:10/L/image.jpg" />');
    }

    public function testMongoDate () {
        $response = $this->compile(
            'MongoDate',
            'helper',
            '{{{MongoDate test format="Y-m-d"}}}',
            ['test' => new MongoDate(strtotime('2000-01-01'))]
        );
        $this->assertTrue($response == '2000-01-01');
    }

    public function testPaginationBootstrap () {
        $response = $this->compile(
            'PaginationBootstrap',
            'helper',
            '{{{PaginationBootstrap pagination metadata}}}',
            [
                'pagination' => ['page' => 1, 'pageCount' => 2, 'limit' => 10],
                'metadata' => ['collection' => 'test', 'method' => 'all']
            ]
        );
        $this->assertTrue($this->normalizeResponse($response) == '<div class="pagination"><ul><li><a href="/test/all/10/1" class="active">1</a></li><li><a href="/test/all/10/2">2</a></li></ul></div>');
    }

    public function testShareThis () {
        /*
        $response = $this->compile(
            'ShareThis',
            'helper',
            '{{{ShareThis}}}',
            []
        );
        $this->assertTrue(true);
        */
    }

    public function testTwitterStream () {
        /*
        $response = $this->compile(
            'TwitterStream',
            'helper',
            '{{{TwitterStream}}}',
            []
        );
        $this->assertTrue(true);
        */
    }

    public function testYoutubeEmbed () {
        $response = $this->compile(
            'YoutubeEmbed',
            'helper',
            '{{{YoutubeEmbed id width="300" height="200"}}}',
            ['id' => 'p3f-eDzkxcw']
        );
        $this->assertTrue($response == '<iframe width="300" height="200" src="https//www.youtube.com/embed/p3f-eDzkxcw" frameborder="0" allowfullscreen></iframe>');
    }

    public function testBuild () {
        $tmp = __DIR__ . '/' . uniqid() . '.php';
        $phpCode = $this->helperRoute->build2(__DIR__ . '/../available/helpers');
        file_put_contents($tmp, $phpCode);
        $helpers = require $tmp;
        $this->assertTrue(is_array($helpers) && count($helpers) > 0);
        unlink($tmp);
    }

    public function testBuildAll () {
        $this->helperRoute->buildAll();
    }
}