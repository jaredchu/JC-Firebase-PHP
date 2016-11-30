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
    public $firebaseDefaultPath;

    public $auth;
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

    public function __construct($firebaseSecret,$firebaseURI,$firebaseDefaultPath = '/')
    {
        $this->firebaseSecret = $firebaseSecret;
        $this->firebaseURI = $firebaseURI;
        $this->firebaseDefaultPath = $firebaseDefaultPath;
    }

    protected function getPathURI(){
        //remove .json or last slash from firebaseURI
        $templates = array(
            '.json',
            '/.json',
            '/'
        );
        foreach ($templates as $template){
            $this->firebaseURI = rtrim($this->firebaseURI,$template);
        }

        //check https
        if(strpos($this->firebaseURI, 'http://') !== false){
            throw new \Exception("");
        }

        if(strpos($this->firebaseURI, 'https://') == false){
            $this->firebaseURI = 'https://'.$this->firebaseURI;
        }
    }

    public function get($option = array()){

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