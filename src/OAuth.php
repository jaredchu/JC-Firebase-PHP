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
    public $tokenLifeTime;
    public $key;
    public $iss;

    protected $exp;
    protected $accessToken;

    /**
     * OAuth constructor.
     * @param $key
     * @param $iss
     */
    public function __construct($key, $iss, $tokenLifeTime = 3600)
    {
        $this->key = $key;
        $this->iss = $iss;
        $this->tokenLifeTime = $tokenLifeTime;
    }

    public function getAccessToken()
    {
        if ($this->exp <= time()) {
            $sTime = time();

            $jsonToken = array(
                "iss" => $this->iss,
                "scope" => "https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email",
                "aud" => "https://www.googleapis.com/oauth2/v4/token",
                "exp" => time() + $this->tokenLifeTime,
                "iat" => time()
            );
            $jwt = JWT::encode($jsonToken, $this->key, 'RS256');

            $OAuthResponse = Requests::post('https://www.googleapis.com/oauth2/v4/token', array(), array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ));

            if ($OAuthResponse->status_code == 200) {
                $this->accessToken = json_decode($OAuthResponse->body)->access_token;

                //set expire time
                $eTime = time();
                $this->exp = $this->tokenLifeTime - ($sTime - $eTime);
            }
        }

        return $this->accessToken;
    }
}