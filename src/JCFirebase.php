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
    public $firebaseURI;
    public $firebaseDefaultPath;
    public $requestHeader = array(
        'accept' => 'application/json',
        'contentType' => 'application/json; charset=utf-8',
        'dataType' => 'json'
    );
    public $requestOptions = array();
    /**
     * @var OAuth
     */
    protected $auth;

    public function __construct($firebaseURI, $firebaseSerivceAccount = '', $firebaseDefaultPath = '/')
    {
        $this->firebaseURI = $firebaseURI;
        $this->firebaseDefaultPath = $firebaseDefaultPath;
        $this->setAuth($firebaseSerivceAccount);
    }

    public function setAuth($firebaseServiceAccount)
    {
        if (isset($firebaseServiceAccount['key']) && isset($firebaseServiceAccount['iss'])) {
            $this->auth = new OAuth($firebaseServiceAccount['key'], $firebaseServiceAccount['iss']);
        }
    }

    protected function refreshToken()
    {
        $this->requestHeader['Authorization'] = 'Bearer ' . $this->auth->getAccessToken();
    }

    protected function mergeRequestPathURI($path = '', $options = array(), $reqType = JCFirebaseOption::REQ_TYPE_GET)
    {
        $print = '';
        if (isset($options['print'])) {
            if (JCFirebaseOption::isAllowPrint($reqType, $options['print'])) {
                $print = $options['print'];
            }
        }
        return $this->getPathURI($path, $print);
    }

    public function getPathURI($path = '', $print = '')
    {
        //remove last slash from firebaseURI
        $template = '/';
        $this->firebaseURI = rtrim($this->firebaseURI, $template);
        $path = rtrim($path, $template);
        $path = ltrim($path, $template);

        //check https
        if (strpos($this->firebaseURI, 'http://') !== false) {
            throw new \Exception("https is required.");
        }

        //check firebaseURI
        if (empty($this->firebaseURI)) {
            throw new \Exception("firebase URI is required");
        }

        if (strpos($this->firebaseDefaultPath, "/") !== 0) {
            throw new \Exception("firebase default path must contain /");
        }

        $pathURI = $this->firebaseURI . $this->firebaseDefaultPath . $path . ".json";

        //set query data
        $queryData = array();
        if (!empty($print)) {
            $queryData[JCFirebaseOption::OPTION_PRINT] = $print;
        }
        if (!empty($queryData)) {
            $pathURI = $pathURI . '?' . http_build_query($queryData);
        }

        $this->refreshToken();

        return $pathURI;
    }

    protected function mergeRequestOptions($options = array(), $jsonEncode = false)
    {
        $requestOptions = array();

        if (isset($options['data'])) {
            $requestOptions = array_merge($options['data'], $requestOptions);
        }

        if ($jsonEncode) {
            $requestOptions = json_encode($requestOptions);
        }

        return $requestOptions;
    }

    public function getShallow($path = '', $options = array())
    {
        return Requests::get($this->getPathURI(
                $path) . '?' . http_build_query(array(JCFirebaseOption::OPTION_SHALLOW => JCFirebaseOption::SHALLOW_TRUE)),
            $this->requestHeader,
            $this->mergeRequestOptions($options)
        );
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function get($path = '', $options = array())
    {
        return Requests::get(
            $this->mergeRequestPathURI($path, $options), $this->requestHeader,
            $this->mergeRequestOptions($options)
        );
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function put($path = '', $options = array())
    {
        return Requests::put($this->getPathURI($path), $this->requestHeader,
            $this->mergeRequestOptions($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function post($path = '', $options = array())
    {
        return Requests::post($this->getPathURI($path), $this->requestHeader,
            $this->mergeRequestOptions($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function patch($path = '', $options = array())
    {
        return Requests::patch($this->getPathURI($path), $this->requestHeader,
            $this->mergeRequestOptions($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     * @return \Requests_Response
     */
    public function delete($path = '', $options = array())
    {
        return Requests::delete($this->getPathURI($path), $this->requestHeader, $this->mergeRequestOptions($options));
    }
}