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
    const OPTION_SHALLOW = 'shallow';

    public $firebaseSecret;
    public $firebaseURI;
    public $firebaseDefaultPath;

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
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function get($path = '',$options = array()){
        return Requests::get($this->getPathURI($path),$this->requestHeader,$this->mergeRequestOptions($options));
    }

    public function getShallow($path = '',$options = array()){
        return Requests::get($this->getPathURI($path). '?' . http_build_query(array(self::OPTION_SHALLOW => 'true')),
            $this->mergeRequestOptions($options));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function put($path = '',$options = array()){
        return Requests::put($this->getPathURI($path),$this->requestHeader,$this->mergeRequestOptions($options,true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function post($path = '',$options = array()){
        return Requests::post($this->getPathURI($path),$this->requestHeader,$this->mergeRequestOptions($options,true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function patch($path = '',$options = array()){
        return Requests::patch($this->getPathURI($path),$this->requestHeader,$this->mergeRequestOptions($options,true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function delete($path = '',$options = array()){
        return Requests::delete($this->getPathURI($path),$this->requestHeader,$this->mergeRequestOptions($options));
    }

    protected function mergeRequestOptions($options = array(),$encode = false){
        $requestOptions = array();

        if(isset($options['data'])){
            $requestOptions = array_merge($options['data'],$requestOptions);
        }

        if($encode){
            $requestOptions = json_encode($requestOptions);
        }

        return $requestOptions;
    }
}