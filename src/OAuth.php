<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 12/2/16
 * Time: 4:25 PM
 */

namespace JCFirebase;

use Firebase\JWT\JWT;
use JC\JCRequest;

class OAuth
{
    /*
     * token life time in second
     * */
    public $tokenLifeTime;
    public $key;
    public $iss;

    protected $expireTimestamp;
    protected $accessToken;

    protected $cache;
    const CACHE_KEY = 'firebase-oauth';

    /**
     * OAuth constructor.
     *
     * @param $key
     * @param $iss
     * @param $lifeTime
     */
    public function __construct($key, $iss, $lifeTime = 3600, $cache = false)
    {
        $this->key = $key;
        $this->iss = $iss;
        $this->tokenLifeTime = $lifeTime;
        $this->cache = $cache;
    }

    public static function fromJson($jsonString, $lifeTime = 3600, $cache = false)
    {
        if ($jsonString) {
            $privateKey = $jsonString->private_key;
            $serviceAccount = $jsonString->client_email;

            return new static($privateKey, $serviceAccount, $lifeTime, $cache);
        } else {
            throw new \Exception("can't get data from key file");
        }
    }

    public static function fromKeyFile($keyFile, $lifeTime = 3600, $cache = false)
    {
        try {
            $jsonString = json_decode(file_get_contents($keyFile));
        } catch (\Exception $exception) {
            $jsonString = json_decode(JCRequest::get($keyFile));
        }

        return static::fromJson($jsonString, $lifeTime, $cache);
    }

    protected function requestAccessToken()
    {
        $currentTimestamp = time();
        $this->expireTimestamp = $currentTimestamp + $this->tokenLifeTime;
        $jsonToken = array(
            "iss" => $this->iss,
            "scope" => "https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email",
            "aud" => "https://www.googleapis.com/oauth2/v4/token",
            "exp" => $this->expireTimestamp,
            "iat" => $currentTimestamp
        );
        $jwt = JWT::encode($jsonToken, $this->key, 'RS256');

        $OAuthResponse = JCRequest::post('https://www.googleapis.com/oauth2/v4/token', array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ));

        if ($OAuthResponse->status() == 200) {
            $this->accessToken = json_decode($OAuthResponse->body())->access_token;

            return true;
        }

        return false;
    }

    public function getAccessToken()
    {
        $currentTime = time();
        if ($this->expireTimestamp < $currentTime) {
            $startTime = time();
            $this->requestAccessToken();
            $endTime = time();
            $this->expireTimestamp -= ($endTime - $startTime);
        }

        return $this->accessToken;
    }
}