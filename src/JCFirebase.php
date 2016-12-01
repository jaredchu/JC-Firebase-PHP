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

    public $requestHeader = array(
        'accept' => 'application/json',
        'contentType' => 'application/json; charset=utf-8',
        'dataType' => 'json'
    );
    public $requestOptions = array();

    public function __construct($firebaseURI,$firebaseSecret = '',$firebaseDefaultPath = '/')
    {
        $this->firebaseSecret = $firebaseSecret;
        $this->firebaseURI = $firebaseURI;
        $this->firebaseDefaultPath = $firebaseDefaultPath;
    }

    public function getPathURI($path = ''){
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
            throw new \Exception("https is required.");
        }

        //check firebaseURI
        if(strlen($this->firebaseURI) == 0){
            throw new \Exception("firebase URI is required");
        }

        $pathURI = $this->firebaseURI.$this->firebaseDefaultPath.$path.".json";
        return $pathURI;
    }


    /**
     * @param array $option
     * @return \Requests_Response
     */
    public function get($path = '',$options = array()){
        if(isset($options['settings'])) {
            $requestOptions = array_merge($options['settings'],$this->requestOptions);
        }
        else{
            $requestOptions = $this->requestOptions;
        }

        return Requests::get($this->getPathURI($path),$this->requestHeader,$requestOptions);
    }

    public function put($path = '',$options = array()){
        $requestOptions = array_merge($options['data'],$this->requestOptions);
        return Requests::put($this->getPathURI($path),$this->requestHeader,json_encode($requestOptions));
    }

    public function post($path = '',$options = array()){
        $requestOptions = array_merge($options['data'],$this->requestOptions);
        return Requests::post($this->getPathURI($path),$this->requestHeader,json_encode($requestOptions));
    }

    public function patch($path = '',$options = array()){
        return Requests::patch($this->getPathURI($path),$this->requestHeader,$this->requestOptions);
    }

    public function delete($path = '',$options = array()){
        return Requests::delete($this->getPathURI($path),$this->requestHeader,$this->requestOptions);
    }
}