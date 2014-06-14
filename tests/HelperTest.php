<?php
namespace Opine;
use Exception;
use LightnCandy;

class HelperTest extends \PHPUnit_Framework_TestCase {
    private $db;
    private $blurbId = 'blurbs:538887dded88e5a5527c1ef6';
    private $helperRoute;

    public function setup () {
        $this->helperRoute = new helperRoute(false, false);
        date_default_timezone_set('UTC');
        $root = __DIR__ . '/../public';
        $container = new Container($root, $root . '/../container.yml');
        $this->db = $container->db;
        $this->ensureDocuments();
    }

    private function ensureDocuments () {
        $this->db->documentStage($this->blurbId, [
            'title' => 'Test',
            'body' => 'Test Blurb',
            'tags' => ['Test']
        ])->upsert();
    }

    private function compile ($name, $type, $partial, $context) {
        $helpers = [];
        $hbhelpers = [];
        switch ($type) {
            case 'helper':
                $helpers[$name] = $this->helperGet($name);
                break;

            case 'hbhelper':
                $hbhelpers[$name] = $this->hbhelperGet($name);
                break;

            default:
                throw new Exception('unknown helper type');
        }
        $php = LightnCandy::compile(
            $partial, 
            [
                'flags' => LightnCandy::FLAG_ERROR_LOG | LightnCandy::FLAG_STANDALONE | LightnCandy::FLAG_HANDLEBARSJS,
                'helpers' => $helpers,
                'hbhelpers' => $hbhelpers
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

    private function hbhelperGet ($hbhelper) {
        return require __DIR__ . '/../available/hbhelpers/' . $hbhelper . '.php';
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
        /*
        $response = $this->compile(
            'EachRowConditionalClass',
            'helper',
            '{{{EachRowConditionalClass}}}',
            []
        );
        $this->assertTrue($response === '');
        */   
    }

    public function testImageResize () {
        /*
        $response = $this->compile(
            'ImageResize',
            'helper',
            '{{{ImageResize test height="10" width="20"}}}',
            ['test' => ['url' => 'image.jpg', 'width' => 50, 'height' => 100]]
        );
        echo $response;
        $this->assertTrue($response === '');
        */
    }

    public function testMongoDate () {
        /*
        $response = $this->compile(
            'MongoDate',
            'helper',
            '{{{MongoDate test format="Y-m-d"}}}'
            ['test' => new \MongoDate(strototime('2000-01-01'))]
        );
        */
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

    public function testBuild () {
        $tmp = __DIR__ . '/' . uniqid() . '.php';
        $phpCode = $this->helperRoute->build2(__DIR__ . '/../available/helpers');
        file_put_contents($tmp, $phpCode);
        $helpers = require $tmp;
        $this->assertTrue(is_array($helpers) && count($helpers) > 0);
        unlink($tmp);
    }
}