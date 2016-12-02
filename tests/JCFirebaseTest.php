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
use Firebase\JWT\JWT;
use JCFirebase\OAuth;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const SERVICE_ACCOUNT_KEY = "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC6cdNFcqYSL57q\nV9Xq6oU6g3sZy/TvWy3SlhCvS5GxDeqJk5k4vgUUtYV54FYop7uhDmOwFBrtrDnl\nhvqXaER65z1jtDr5qG5ZaVW+sysU5N9Ri/27gDsPhQebvh8MZvhrS43t0OfeYkhP\n6FEF+ASL8Fmqh8+HsLWbZEKcz0i8iUWz3VU63RPl1uugCpQ8/HWRIf1UCzo1MIQS\nhXTs8XbE+WmlURPIQCXUoYmRjtKOdvY86mlq0hg2rfNLjWysKCAUqhpyjqtqCYv8\npsQ5+2LWLPY/hCEj0deZOYfkuMTI63PsSLx/V/3bwj0/LgIK5reYQzt04ZJC29Xs\nat4OfC5VAgMBAAECggEACclPvaV28h1tyf8yS1JkC9465++8u5OdpCAcYBcnXpPM\na8xDjqbqvVuJ1gvgeeHUB3Ap62Qixbb0kweyv5/JeGoK3uDm95PPtwHAbnIZIM+i\nqTUf7GeesgHZlGM2XgBJqdgdaw48HYArK4aWOin6kx3alwjp68Vqwu1QUcrPlpXG\n4u9OUcR6V5cwZ8dtT68V68iHRA9bGY/1y+2Z6GEy+Yk36svGq54VB53bE20bKr8x\nWKC9x4VFocktU4vJTVqh+ZnE47pf2jBTgJtt1xNZS4ULeMnaKhzbgrlfexFSRG98\nOhPjdbxp1lvHVz6GAOSANOdMR8YRvbTL/BbWB60WAQKBgQD4aa6RnpSs56AMOHsW\nZsl1CZJG0tQUOltBLtSBy5ZcqbSXg6TOhx96y05xh2Am1cr1iMboVkznJxwWcx1E\nZZCTfJ7hac0gQM54w+t0p3uy9PKnGe8vuNsP//qZjHcUgCWFMgxF5nRm9a8G7T8+\n+5Dfiyqycj+sDj0XBtIzl0Qv2QKBgQDAI56YqlzvBm9uXXO6CHoG3WgxZob81Rkt\nw0odjOH/HL4p8cUqgkMmceQonThNEmTaBPNmRRyfRT1jo16ICsOnqjiU53ilBE5M\nTj5nMyeMPjnJ8y0h8P2BYOtrCZxhRRIWf+InS4DLFUj2/PTrlksLvfOJFFFqF46L\noYRELSng3QKBgQDoxBYYSvDjJ1LXwKDNd9jzetl+1ZR1s8zIQjpLuNmW0Enw4yCt\niP2dUR2a0Y/uM8wu0FAZaqw6uHHgM1NMQKL9H/u8GihkPXV6wZJCR6FoKY+ptmpi\nXCOgVWqbMHxwUDdYHHJVmUCfLT0pD2uA97ZjUQLcHKO+88SYbALQ5r+02QKBgQCg\nHk7xnK+bxfoguCJT+vJuPB6zZGTD2HaGK4PdQmWnqvVSkRelSC7bn/fqXLLpj5Wa\nFpwcMyYaGKKDIUcLCiVo2TMT7B4fLoptjpr0d63ScpzIDi4jZehs5C7r4XN/oAbl\n1ybJZmNrWk6W/dDBb1sU6vRD3TnHtt/kvvIxuo12xQKBgQCF+VDhDSw5lF3i9A0Y\napsQR9c2/zXbpMfjoq8/zz2vLkcdrdlFNKszHYilYrRDonY2Mrt+1nE444ir43mn\n760ypqS07+6taeN2dCw/AO8ocnfvYyDyNrC8ltGYqK9CkalcBjgmFGg9vYK/izfm\n9hBEbFyynBa0Hprb4CXPtlUIrQ==\n-----END PRIVATE KEY-----\n";
    const SERVICE_ACCOUNT_EMAIL = "admin-883@fir-php-test-c7fa2.iam.gserviceaccount.com";
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';

    public $firebase;

    private function data(){
        return array(
            'number' => 1,
            'string' => 'hello'
        );
    }
    
    private function getFirebase(){
    	if($this->firebase){
    	    return $this->firebase;
        }else{
    	    $this->firebase = new JCFirebase(self::FIREBASE_URI,array(
                'key' => self::SERVICE_ACCOUNT_KEY,
                'iss' => self::SERVICE_ACCOUNT_EMAIL
            ));
        }
        return $this->firebase;
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