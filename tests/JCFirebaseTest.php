<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */

use JCFirebase\JCFirebase;
use JCFirebase\JCFirebaseOption;
use PHPUnit\Framework\TestCase;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const FIREBASE_SECRET = '';
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';

    private function data(){
        return array(
            'number' => 1,
            'string' => 'hello'
        );
    }
    
    private function getFirebase(){
    	return new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
	}

	public function testGetPathURI(){
    	$firebase = self::getFirebase();

    	self::assertEquals(self::FIREBASE_URI.'.json',$firebase->getPathURI());
		self::assertEquals(self::FIREBASE_URI.'.json',$firebase->getPathURI('/'));

		self::assertEquals(self::FIREBASE_URI.'path.json',$firebase->getPathURI('path'));
		self::assertEquals(self::FIREBASE_URI.'path.json',$firebase->getPathURI('/path/'));
		self::assertEquals(self::FIREBASE_URI.'path.json',$firebase->getPathURI('//path//'));

		self::assertEquals(self::FIREBASE_URI.'path/to/your.json',$firebase->getPathURI('path/to/your'));
		self::assertEquals(self::FIREBASE_URI.'path/to/your.json',$firebase->getPathURI('/path/to/your/'));
		self::assertEquals(self::FIREBASE_URI.'path/to/your.json',$firebase->getPathURI('//path/to/your//'));

	}

    public function testGet(){
        $firebase = self::getFirebase();

        $response = $firebase->get();
        self::assertEquals(200,$response->status_code);
    }

    public function testPut(){
        $firebase = self::getFirebase();
        $subPath = 'put_test';

        $response = $firebase->put($subPath,array(
            'data' => self::data()
        ));

        self::assertEquals(200,$response->status_code);
        self::assertEquals(1,json_decode($response->body)->number);
        self::assertEquals('hello',json_decode($response->body)->string);
    }

    public function testPost(){
        $firebase = self::getFirebase();
        $subPath = 'post_test';

        $response = $firebase->post($subPath,array(
            'data' => self::data()
        ));

        self::assertEquals(200,$response->status_code);
        self::assertNotNull(json_decode($response->body)->name);
    }

    public function testPatch()
    {
        $firebase = new JCFirebase(self::FIREBASE_URI, self::FIREBASE_SECRET);
        $subPath = 'patch_test';

        $firebase->put($subPath, array(
            'data' => self::data()
        ));

        $response = $firebase->patch($subPath, array(
            'data' => array(
                'number' => 2,
                'string' => 'hello2'
            )
        ));

        self::assertEquals(200, $response->status_code);
        self::assertEquals(2, json_decode($response->body)->number);
        self::assertEquals('hello2', json_decode($response->body)->string);
    }

    public function testDelete(){
        $firebase = self::getFirebase();
        $subPath = 'delete_test';

        $firebase->put($subPath,array(
            'data' => self::data()
        ));

        $subPath = 'delete_test/number';

        $response = $firebase->delete($subPath);

        self::assertEquals(200,$response->status_code);
    }

    public function testGetShallow(){
        $firebase = self::getFirebase();
        $subPath = 'get_shallow_test';

        $firebase->put($subPath,array('data' => self::data()));

        $response = $firebase->getShallow($subPath);
        self::assertEquals(200,$response->status_code);
        self::assertTrue(json_decode($response->body)->number);
        self::assertTrue(json_decode($response->body)->string);
    }

    public function testGetPrint(){
        $firebase = self::getFirebase();

        self::assertContains(" ",$firebase->get(null,array(
            JCFirebaseOption::OPTION_PRINT=>JCFirebaseOption::PRINT_PRETTY
        ))->body);

        self::assertEquals(204,$firebase->get(null,array(
            JCFirebaseOption::OPTION_PRINT=>JCFirebaseOption::PRINT_SILENT
        ))->status_code);
    }
}