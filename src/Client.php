<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 18/09/2017
 * Time: 16:29
 */

namespace JC\Firebase;


use JC\HttpClient\JCRequest;

class Client
{
    public static function getClient()
    {
        return new JCRequest();
    }
}