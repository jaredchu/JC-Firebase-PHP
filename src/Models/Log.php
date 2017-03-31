<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 1/15/17
 * Time: 11:03 AM
 */

namespace JCFirebase\Models;

class Log extends FirebaseModel
{
    public static $nodeName = 'data/log';

    /**
     * @var integer
     */
    public $code;
    /**
     * @var string
     */
    public $message;
}