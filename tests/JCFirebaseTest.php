<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */

use JCFirebase\JCFirebase;
use JCFirebase\JCFirebaseOption;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_ACCOUNT_KEY = "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDGBLBTjqaSx/2+\nbuyLuh7xbQGDBrcCRAB2a3LXv64IuxklwD2913KrxSYaeVThmbHa2AGSPkj7arBh\nIlRWC+fNS1OBNvkPuVoP8vCcK4vDx6OgeqyMlsgh53fJKoz3mNp4EEDNyH5iNEVw\nKbpBQUghcBZh1EIFvF4dwCqL7XMCMKkn3cJZ+CDp83z+tRkO+p3d+9rAIIizc0bU\nB2LQikwJrNdkPQ9GVEWfphwrjjlW50SHPm8JOhMN7vYJf0oAk8LV9H6LIK2nB28L\nMPYmBj/1PRMl0m9It2olDmBZ2ytB2Ooj48k0vVXVpOA4pr5hiD51OE8/Gul7BH9/\nvwjuPThPAgMBAAECggEBAMOvjIT2evyg8MxJmC+5Ha0eORlAAIkXpJFiO9qkDRuJ\nsh9RbHJ4QFUpfi85aj2MAmwvfNYGAV+cHnPPViK2nzuMzhfquJTmae9K+KaMjhFK\n8BO+R1ikBWEj+odtKmPgxT1TaocyNFteJqTiR7MHDX4l46iH9zrt3OBvsFwZqsck\ni7tqL6o8NxqiCJmfybENFoZ3mg309HtkZT76ipYTLbxlqGzubnTBiBEg9cCDVYdc\n8bKJHnLghKBoAkB6W/+op3LRy6PSnRkHyfWOUSwAsoQMa41u/1r5AZsFPB8tF38P\nAwDxDPvLMFFTPDluCOZrZirFARxygGmI8x0GQOA3KvECgYEA7mBRg/a7yQxxExMc\nCbIM721m0Us+Z+X2978kyXEPEFxWthnlKK+ythKBtGXNh0Wr8RVWFpbTCl64PT7/\nBEY1Pb/uHBnXBmuRT3J9bOZg4tcmkG7f1zHhQYiHAIolOpiBeLgGSRBMCaLNGuU2\nzM8N/mLvnmFuI7OQ9pJmXA4kpYkCgYEA1KiG5jZA/YWSbu8KCUzUasY84W7Iv+5j\nhs5WV2QlhLe9EHod7JeGQ4R9Uay6PZW44IJF0PhjmNRqH/GuqfhAPMbGdnf7kCA6\nNy5jEi67jm/g56sjeobVGNX9l7WmPLIvkkbdAbrhMxrAqjAKE3IKQjKEmWmWifVA\nG2R8HrmCURcCgYBko8umkEPl9NfEetvqh/6IE0NGd6MIUIG9RTjtx0rZ2HJPfY1P\naSZlUljqZdSpGNQn+58V+GVvSmA0k2UtU8rkoCSSPqKWtlFqHmcv0+/xtW41qmnc\nu9VjSpXct3ZST9LRubgFntjLdK1tfnpta7l3viN2VaIfdo9sWpDWqq30KQKBgAMw\nj+1uDOWAlmSxky32iA0d8hXTipFOaxG/kI35A5MNCnnvyvkcgLgMibCq7ZQ05bQA\nYDm1MBE/xmO6RUtpXNVMifeG7zAHO7hOKtBAATIuvWncKEkTMqkPtKEM6XRpm8sO\n4wu+mNgiY5dp5wzJnhvGFDUU31wsYIzIog/36lt7AoGBALoCn5jAx3UkS3zPJSy8\n8svC7xbmqHlTY/O6FcbDA5D7TYyF1dTxehvnEpf8SSeBMF8uA4bIq4XLoyFh8+Q+\nMJvJY6g8r3fGc2lzKztSFEceBVRW9as4QFPc+kQiY2656qU4EfQ7sh2wGuPceuNy\n3eI4K3++1UDcSdGguCV7qXfb\n-----END PRIVATE KEY-----\n";
    const SERVICE_ACCOUNT_EMAIL = "firebase-adminsdk-0kp6l@fir-php-test-c7fa2.iam.gserviceaccount.com";
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';

    public $firebase;

    public function testGetPathURI()
    {
        $firebase = self::getFirebase();

        self::assertEquals(self::FIREBASE_URI . '.json', $firebase->getPathURI());
        self::assertEquals(self::FIREBASE_URI . '.json', $firebase->getPathURI('/'));

        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('path'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('/path/'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('//path//'));

        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('path/to/your'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('/path/to/your/'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('//path/to/your//'));

    }

    private function getFirebase()
    {
        if ($this->firebase) {
            return $this->firebase;
        } else {
            $this->firebase = new JCFirebase(self::FIREBASE_URI, array(
                'key' => self::SERVICE_ACCOUNT_KEY,
                'iss' => self::SERVICE_ACCOUNT_EMAIL
            ));
        }
        return $this->firebase;
    }

    public function testGet()
    {
        $firebase = self::getFirebase();

        $response = $firebase->get();
        self::assertEquals(200, $response->status_code);
    }

    public function testPut()
    {
        $firebase = self::getFirebase();
        $subPath = 'put_test';

        $response = $firebase->put($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status_code);
        self::assertEquals(1, json_decode($response->body)->number);
        self::assertEquals('hello', json_decode($response->body)->string);
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
        $firebase = self::getFirebase();
        $subPath = 'post_test';

        $response = $firebase->post($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status_code);
        self::assertNotNull(json_decode($response->body)->name);
    }

    public function testPatch()
    {
        $firebase = self::getFirebase();
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

    public function testDelete()
    {
        $firebase = self::getFirebase();
        $subPath = 'delete_test';

        $firebase->put($subPath, array(
            'data' => self::data()
        ));

        $subPath = 'delete_test/number';

        $response = $firebase->delete($subPath);

        self::assertEquals(200, $response->status_code);
    }

    public function testGetShallow()
    {
        $firebase = self::getFirebase();
        $subPath = 'get_shallow_test';

        $firebase->put($subPath, array('data' => self::data()));

        $response = $firebase->getShallow($subPath);
        self::assertEquals(200, $response->status_code);
        self::assertTrue(json_decode($response->body)->number);
        self::assertTrue(json_decode($response->body)->string);
    }

    public function testGetPrint()
    {
        $firebase = self::getFirebase();

        self::assertContains(" ", $firebase->get(null, array(
            JCFirebaseOption::OPTION_PRINT => JCFirebaseOption::PRINT_PRETTY
        ))->body);

        self::assertEquals(204, $firebase->get(null, array(
            JCFirebaseOption::OPTION_PRINT => JCFirebaseOption::PRINT_SILENT
        ))->status_code);
    }
}