<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */
require_once "../src/JCFirebase.php";

use JCFirebase\JCFirebase;
use PHPUnit\Framework\TestCase;

class JCFirebaseTest extends TestCase
{
    const FIREBASE_SECRET = '';
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';

    public function testGet(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);

        $request = $firebase->get();
        self::assertEquals(200,$request->status_code);
    }

    public function testPut(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $sub_path = 'put_test';

        $data = array(
            'number' => 1,
            'string' => 'hello'
        );
        $request = $firebase->put($sub_path,array(
            'data' => $data
        ));
        self::assertEquals(200,$request->status_code);

        $getRequest = $firebase->get($sub_path);
        self::assertEquals(1,json_decode($getRequest->body)->number);
        self::assertEquals('hello',json_decode($getRequest->body)->string);
    }

    public function testPost(){
        $firebase = new JCFirebase(self::FIREBASE_URI,self::FIREBASE_SECRET);
        $sub_path = 'post_test/';

        $data = array(
            'number' => 1,
            'string' => 'hello',
            'order' => time()
        );
        $request = $firebase->post($sub_path,array(
            'data' => $data
        ));

        self::assertEquals(200,$request->status_code);
        self::assertNotNull(json_decode($request->body)->name);

        $getRequest = $firebase->get($sub_path,array(
            'settings' => array(
                'orderBy' => 'order'
            )
        ));

        $firstObject = reset(json_decode($getRequest->body));
        self::assertEquals(1,$firstObject->number);
        self::assertEquals('hello',$firstObject->string);
    }
}