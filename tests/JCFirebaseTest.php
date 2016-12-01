<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */

use JCFirebase\JCFirebase;
use PHPUnit\Framework\TestCase;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const FIREBASE_SECRET = '';
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';
    const TEST_DATA = array(
        'number' => 1,
        'string' => 'hello'
    );

    public function testGet(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);

        $request = $firebase->get();
        self::assertEquals(200,$request->status_code);
    }

    public function testPut(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $sub_path = 'put_test';

        $request = $firebase->put($sub_path,array(
            'data' => self::TEST_DATA
        ));

        self::assertEquals(200,$request->status_code);
        self::assertEquals(1,json_decode($request->body)->number);
        self::assertEquals('hello',json_decode($request->body)->string);
    }

    public function testPost(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $sub_path = 'post_test';

        $request = $firebase->post($sub_path,array(
            'data' => self::TEST_DATA
        ));

        self::assertEquals(200,$request->status_code);
        self::assertNotNull(json_decode($request->body)->name);
    }

    public function testPatch()
    {
        $firebase = new JCFirebase(self::FIREBASE_URI, self::FIREBASE_SECRET);
        $sub_path = 'patch_test';

        $firebase->put($sub_path, array(
            'data' => self::TEST_DATA
        ));

        $request = $firebase->patch($sub_path, array(
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
        $sub_path = 'delete_test';

        $firebase->put($sub_path,array(
            'data' => self::TEST_DATA
        ));

        $sub_path = 'delete_test/number';

        $request = $firebase->delete($sub_path);

        self::assertEquals(200,$request->status_code);
    }
}