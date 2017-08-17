<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */

require(__DIR__.'/../vendor/autoload.php');

use JCFirebase\Enums\PrintType;
use JCFirebase\JCFirebase;
use JCFirebase\Option;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';
    const KEY_FILE = __DIR__ . '/../resource/firebase-php-test-0a49b34e5f4a.json';

    /**
     * @var JCFirebase
     */
    protected static $firebase;

    public static function setUpBeforeClass()
    {
        self::$firebase = JCFirebase::fromKeyFile(self::FIREBASE_URI, self::KEY_FILE);
    }

    public function testGetPathURI()
    {
        $fb = self::$firebase;

        self::assertEquals(self::FIREBASE_URI . '.json', $fb->getPathURI());
        self::assertEquals(self::FIREBASE_URI . '.json', $fb->getPathURI('/'));

        self::assertEquals(self::FIREBASE_URI . 'path.json', $fb->getPathURI('path'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $fb->getPathURI('/path/'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $fb->getPathURI('//path//'));

        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $fb->getPathURI('path/to/your'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $fb->getPathURI('/path/to/your/'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $fb->getPathURI('//path/to/your//'));

    }

    public function testGet()
    {
        $fb = self::$firebase;

        $response = $fb->get();
        self::assertEquals(200, $response->status());
    }

    public function testPut()
    {
        $fb = self::$firebase;
        $subPath = 'put_test';

        $response = $fb->put($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status());
        self::assertEquals(1, $response->json()->number);
        self::assertEquals('hello', $response->json()->string);
    }

    private function data()
    {
        return array(
            'number' => 1,
            'string' => 'hello'
        );
    }

    public function testPost()
    {
        $fb = self::$firebase;
        $subPath = 'post_test';

        $response = $fb->post($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status());
        self::assertNotNull($response->json()->name);
    }

    public function testPatch()
    {
        $fb = self::$firebase;
        $subPath = 'patch_test';

        $fb->put($subPath, array(
            'data' => self::data()
        ));

        $response = $fb->patch($subPath, array(
            'data' => array(
                'number' => 2,
                'string' => 'hello2'
            )
        ));

        self::assertEquals(200, $response->status());
        self::assertEquals(2, $response->json()->number);
        self::assertEquals('hello2', $response->json()->string);
    }

    public function testDelete()
    {
        $fb = self::$firebase;
        $subPath = 'delete_test';

        $fb->put($subPath, array(
            'data' => self::data()
        ));

        $subPath = 'delete_test/number';

        $response = $fb->delete($subPath);

        self::assertEquals(200, $response->status());
    }

    public function testGetShallow()
    {
        $fb = self::$firebase;
        $subPath = 'get_shallow_test';

        $fb->put($subPath, array('data' => self::data()));

        $response = $fb->getShallow($subPath);
        self::assertEquals(200, $response->status());
        self::assertTrue($response->json()->number);
        self::assertTrue($response->json()->string);
    }

    public function testGetPrint()
    {
        $fb = self::$firebase;

        self::assertContains(" ", $fb->get(null, array(
            Option::OPT_PRINT => PrintType::PRETTY
        ))->body());

        self::assertTrue($fb->isValid());
    }
}