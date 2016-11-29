<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 3:47 PM
 */

namespace JCFirebase;

use Requests;

/**
 * Class JCFirebase
 * @package JCFirebase
 * reference https://www.firebase.com/docs/rest/api/
 */
class JCFirebase
{
    public $firebaseSecret;
    public $firebaseURI;

    protected $auth;
    public $shallow;
    public $print;
    public $callback;
    public $format;
    public $download;
    public $orderBy;
    public $limitToFirst;
    public $limitToLast;
    public $startAt;
    public $endAt;
    public $equalTo;

    public $streaming = 'text/event-stream';
    public $priority = '.priority';
    public $serverValues = '.sv';

    public $rulePath = '.settings/rules.json';

    public function get(){

    }

    public function put(){

    }

    public function post(){

    }

    public function patch(){

    }

    public function delete(){

    }
}