<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 12/2/16
 * Time: 4:25 PM
 */

namespace JCFirebase;

use Firebase\JWT\JWT;
use Requests;

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

    /**
     * OAuth constructor.
     *
     * @param $key
     * @param $iss
     * @param $lifeTime
     */
    public function __construct($key, $iss, $lifeTime = 3600)
    {
        $this->key = $key;
        $this->iss = $iss;
        $this->tokenLifeTime = $lifeTime;
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

        $OAuthResponse = Requests::post('https://www.googleapis.com/oauth2/v4/token', array(), array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ));

        if ($OAuthResponse->status_code == 200) {
            $this->accessToken = json_decode($OAuthResponse->body)->access_token;

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