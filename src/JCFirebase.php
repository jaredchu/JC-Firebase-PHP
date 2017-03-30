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

    public $rootPath;

    public $requestHeader = array(
        'accept' => 'application/json',
        'contentType' => 'application/json; charset=utf-8',
        'dataType' => 'json'
    );

    public $requestOptions = array();

    /**
     * @var OAuth
     */
    public $auth;


    /**
     * JCFirebase constructor.
     *
     * @param $firebaseURI
     * @param array $firebaseAuth
     * @param string $rootPath
     */
    public function __construct($firebaseURI, OAuth $auth, $rootPath = '/')
    {
        $this->firebaseURI = $firebaseURI;
        $this->firebaseDefaultPath = $rootPath;
        $this->auth = $auth;
    }


    /**
     * @param $firebaseURI
     * @param $jsonString
     * @param string $rootPath
     * @return JCFirebase
     * @throws \Exception
     */
    public static function fromJson($firebaseURI, $jsonString, $rootPath = '/')
    {
        if ($jsonString) {
            $serviceAccount = $jsonString->client_email;
            $privateKey = $jsonString->private_key;

            return new self($firebaseURI, new OAuth($privateKey, $serviceAccount), $rootPath);
        } else {
            throw new \Exception("can't get data from key file");
        }
    }

    /**
     * @param $firebaseURI
     * @param $keyFile
     * @param string $rootPath
     *
     * @return JCFirebase
     * @throws \Exception
     */
    public static function fromKeyFile($firebaseURI, $keyFile, $rootPath = '/')
    {
        $jsonString = null;
        try {
            $jsonString = json_decode(file_get_contents($keyFile));
        } catch (\Exception $exception) {
            $jsonString = json_decode(Requests::get($keyFile));
        }

        return self::fromJson($firebaseURI, $jsonString, $rootPath);
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
            $queryData[Option::OPTION_PRINT] = $print;
        }
        if (!empty($queryData)) {
            $pathURI = $pathURI . '?' . http_build_query($queryData);
        }

        $this->refreshToken();

        return $pathURI;
    }

    public function getShallow($path = '', $options = array())
    {
        return Requests::get(
            $this->getPathURI($path) . '?' . http_build_query(array(
                Option::OPTION_SHALLOW => Option::SHALLOW_TRUE
            )),
            $this->requestHeader,
            $this->addDataToRequest($options)
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return \Requests_Response
     */
    public function get($path = '', $options = array())
    {
        return Requests::get(
            $this->addDataToPathURI($path, $options), $this->requestHeader,
            $this->addDataToRequest($options)
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return \Requests_Response
     */
    public function put($path = '', $options = array())
    {
        return Requests::put($this->getPathURI($path), $this->requestHeader,
            $this->addDataToRequest($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return \Requests_Response
     */
    public function post($path = '', $options = array())
    {
        return Requests::post($this->getPathURI($path), $this->requestHeader,
            $this->addDataToRequest($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return \Requests_Response
     */
    public function patch($path = '', $options = array())
    {
        return Requests::patch($this->getPathURI($path), $this->requestHeader,
            $this->addDataToRequest($options, true));
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return \Requests_Response
     */
    public function delete($path = '', $options = array())
    {
        return Requests::delete($this->getPathURI($path), $this->requestHeader,
            $this->addDataToRequest($options));
    }

    /**
     * Function that check firebase authencation
     * and configuration valid or not
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->get(null, array(
                Option::OPTION_PRINT => Option::PRINT_SILENT
            ))->status_code == 204;
    }

    protected function refreshToken()
    {
        $this->requestHeader['Authorization'] = 'Bearer ' . $this->auth->getAccessToken();
    }

    protected function addDataToPathURI($path = '', $options = array(), $reqType = Option::REQ_TYPE_GET)
    {
        $print = '';
        if (isset($options['print'])) {
            if (Option::isAllowPrint($reqType, $options['print'])) {
                $print = $options['print'];
            }
        }

        return $this->getPathURI($path, $print);
    }

    protected function addDataToRequest($options = array(), $jsonEncode = false)
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
}