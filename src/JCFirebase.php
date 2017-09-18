<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 3:47 PM
 */

namespace JC\Firebase;

use JC\Firebase\Enums\PrintType;
use JC\Firebase\Enums\RequestType;
use JC\HttpClient\JCResponseInterface;

/**
 * Class JCFirebase
 * @package JCFirebase
 * reference https://www.firebase.com/docs/rest/api/
 */
class JCFirebase
{
    public $firebaseURI;

    public $rootPath;

    /**
     * @var array
     */
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

    public $client;

    /**
     * JCFirebase constructor.
     *
     * @param $firebaseURI
     * @param OAuth $auth
     * @param string $rootPath
     */
    public function __construct($firebaseURI, OAuth $auth, $rootPath = '/')
    {
        $this->firebaseURI = $firebaseURI;
        $this->rootPath = $rootPath;
        $this->auth = $auth;
        $this->client = Client::getClient();
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
        return new self($firebaseURI, OAuth::fromJson($jsonString), $rootPath);
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
        return new self($firebaseURI, OAuth::fromKeyFile($keyFile), $rootPath);
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

        if (strpos($this->rootPath, "/") !== 0) {
            throw new \Exception("firebase default path must contain /");
        }

        $pathURI = $this->firebaseURI . $this->rootPath . $path . ".json";

        //set query data
        $queryData = array();
        if (!empty($print)) {
            $queryData[Option::OPT_PRINT] = $print;
        }
        if (!empty($queryData)) {
            $pathURI = $pathURI . '?' . http_build_query($queryData);
        }

        $this->refreshToken();

        return $pathURI;
    }

    public function getShallow($path = '', $options = array())
    {
        return $this->client->get(
            $this->getPathURI($path) . '?' . http_build_query(array(
                Option::OPT_SHALLOW => 'true'
            )),
            $this->addDataToRequest($options),
            $this->requestHeader
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return JCResponseInterface
     */
    public function get($path = '', $options = array())
    {
        return $this->client->get(
            $this->addDataToPathURI($path, $options),
            $this->addDataToRequest($options),
            $this->requestHeader
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return JCResponseInterface
     */
    public function put($path = '', $options = array())
    {
        return $this->client->put($this->getPathURI($path),
            $this->addDataToRequest($options, true),
            $this->requestHeader
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return JCResponseInterface
     */
    public function post($path = '', $options = array())
    {
        return $this->client->post(
            $this->getPathURI($path),
            $this->addDataToRequest($options, true),
            $this->requestHeader
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return JCResponseInterface
     */
    public function patch($path = '', $options = array())
    {
        return $this->client->patch(
            $this->getPathURI($path),
            $this->addDataToRequest($options, true),
            $this->requestHeader
        );
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return JCResponseInterface
     */
    public function delete($path = '', $options = array())
    {
        return $this->client->delete(
            $this->getPathURI($path),
            $this->addDataToRequest($options),
            $this->requestHeader
        );
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
                Option::OPT_PRINT => PrintType::SILENT
            ))->status() == 204;
    }

    protected function refreshToken()
    {
        $this->requestHeader['Authorization'] = 'Bearer ' . $this->auth->getAccessToken();
    }

    protected function addDataToPathURI($path = '', $options = array(), $reqType = RequestType::GET)
    {
        $print = '';
        if (isset($options[Option::OPT_PRINT]) && Option::isAllowPrint($reqType, $options['print'])) {
            $print = $options[Option::OPT_PRINT];
        }

        return $this->getPathURI($path, $print);
    }

    protected function addDataToRequest($options = array(), $jsonEncode = false)
    {
        $requestData = array();

        if (isset($options['data'])) {
            $requestData = array_merge($options['data'], $requestData);
        }

        if ($jsonEncode) {
            $requestData = json_encode($requestData);
        }

        return $requestData;
    }
}