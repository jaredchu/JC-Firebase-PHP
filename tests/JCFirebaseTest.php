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

    public function testGet(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);

        $request = $firebase->get();
        self::assertEquals(200,$request->status_code);
    }

    public function testPut(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $subPath = 'put_test';

        $request = $firebase->put($subPath,array(
            'data' => self::data()
        ));

        self::assertEquals(200,$request->status_code);
        self::assertEquals(1,json_decode($request->body)->number);
        self::assertEquals('hello',json_decode($request->body)->string);
    }

    public function testPost(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $subPath = 'post_test';

        $request = $firebase->post($subPath,array(
            'data' => self::data()
        ));

        self::assertEquals(200,$request->status_code);
        self::assertNotNull(json_decode($request->body)->name);
    }

    public function testPatch()
    {
        $firebase = new JCFirebase(self::FIREBASE_URI, self::FIREBASE_SECRET);
        $subPath = 'patch_test';

        $firebase->put($subPath, array(
            'data' => self::data()
        ));

        $request = $firebase->patch($subPath, array(
            'data' => array(
                'number' => 2,
                'string' => 'hello2'
            )
        ));

        self::assertEquals(200, $request->status_code);
        self::assertEquals(2, json_decode($request->body)->number);
        self::assertEquals('hello2', json_decode($request->body)->string);
    }

    public function testDelete(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $subPath = 'delete_test';

        $firebase->put($subPath,array(
            'data' => self::data()
        ));

        $subPath = 'delete_test/number';

        $request = $firebase->delete($subPath);

        self::assertEquals(200,$request->status_code);
    }

    public function testGetShallow(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $subPath = 'get_shallow_test';

        $firebase->put($subPath,array('data' => self::data()));

        $request = $firebase->getShallow($subPath);
        self::assertEquals(200,$request->status_code);
        self::assertTrue(json_decode($request->body)->number);
        self::assertTrue(json_decode($request->body)->string);
    }

    public function testGetPrint(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);

        self::assertContains(" ",$firebase->get(null,array(
            JCFirebaseOption::OPTION_PRINT=>JCFirebaseOption::PRINT_PRETTY
        ))->body);

        self::assertEquals(204,$firebase->get(null,array(
            JCFirebaseOption::OPTION_PRINT=>JCFirebaseOption::PRINT_SILENT
        ))->status_code);
    }
}