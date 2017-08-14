<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 1/15/17
 * Time: 11:01 AM
 */

use JCFirebase\JCFirebase;
use JCFirebase\Models\Log;

class FirebaseModelTest extends PHPUnit_Framework_TestCase
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

    public function testCreateLog()
    {
        $log = new Log(self::$firebase);
        $log->code = 200;
        $log->message = 'Success';

        self::assertTrue($log->create());
        self::assertNotEmpty($log->key);

        $logCopy = Log::findByKey($log->key, self::$firebase);
        $logCopy->setNodeName('data/logCopy');

        self::assertNotEmpty($logCopy->key);
        self::assertEquals($log->code, $logCopy->code);
        self::assertEquals($log->message, $logCopy->message);
    }

    public function testSaveLog()
    {
        $log = new Log(self::$firebase);
        $log->code = 401;
        $log->message = 'Unauthorized';

        self::assertTrue($log->create());
        self::assertNotEmpty($log->key);

        $log->code = 400;
        $log->message = 'Bad Request';
        $log->save();

        $logCopy = Log::findByKey($log->key, self::$firebase);
        self::assertNotEmpty($logCopy->key);
        self::assertEquals(400, $logCopy->code);
        self::assertEquals('Bad Request', $logCopy->message);
    }

    public function testDeleteLog()
    {
        $log = new Log(self::$firebase);
        $log->code = 999;
        $log->message = 'Should Be Deleted';

        self::assertTrue($log->save());
        self::assertTrue($log->delete());

        $logCopy = Log::findByKey($log->key, self::$firebase);
        self::assertNull($logCopy);
    }

    public function testFindAllLog()
    {
        $logs = Log::findAll(self::$firebase);
        self::assertTrue(count($logs) > 0);

        $firstLog = current($logs);
        $firstLog->code = 501;
        self::assertTrue($firstLog->save());

        $firstLogCopy = Log::findByKey($firstLog->key, self::$firebase);
        self::assertEquals($firstLog->code, $firstLogCopy->code);
        self::assertEquals($firstLog->message, $firstLogCopy->message);
    }

    public function testCreateModelWithKey()
    {
        $log = new Log(self::$firebase);
        $log->key = 10;
        $log->code = 202;
        $log->message = "OK";
        $log->save();

        self::assertEquals(10, Log::findByKey(10, self::$firebase)->key);
    }

    public function testAttributeMapping()
    {
        Log::setMaps([
            'code' => 'code_number',
            'message' => 'status'
        ]);

        Log::setNodeName('data/LogAttr');

        $log = new Log(self::$firebase);
        $log->code = 200;
        $log->message = 'Success';

        self::assertTrue($log->create());
        self::assertNotEmpty($log->key);

        $logCopy = Log::findByKey($log->key, self::$firebase);

        self::assertNotEmpty($logCopy->key);
        self::assertEquals($log->code, $logCopy->code);
        self::assertEquals($log->message, $logCopy->message);
    }
}