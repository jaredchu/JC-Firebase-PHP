<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 1/14/17
 * Time: 2:25 PM
 */

namespace JCFirebase\Models;

use JCFirebase\JCFirebase;
use JsonMapper;

/**
 * Class FirebaseModel
 * @package JCFirebase
 */
class FirebaseModel
{

    /**
     * @var string
     */
    public static $nodeName = '';

    /**
     * @var string
     */
    public $key;

    /**
     * @var JCFirebase
     */
    public $firebase;

    /**
     * FirebaseModel constructor.
     *
     * @param \JCFirebase\JCFirebase $firebase
     */
    public function __construct(JCFirebase $firebase = null)
    {
        $this->firebase = $firebase;
    }

    public static function getNodeName()
    {
        return static::$nodeName ?: strtolower((new \ReflectionClass(get_called_class()))->getShortName());
    }

    public static function setNodeName($nodeName)
    {
        static::$nodeName = $nodeName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $object = clone $this;
        unset($object->firebase);
        unset($object->key);

        return get_object_vars($object);
    }

    /**
     * @return bool
     */
    public function create()
    {
        $response = $this->firebase->post(self::getNodeName(), array(
            'data' => $this->getData()
        ));

        $this->key = json_decode($response->body())->name;

        return $response->success();
    }


    /**
     * @return bool
     */
    public function save()
    {
        if (!empty($this->key)) {
            $response = $this->firebase->put(self::getNodeName() . '/' . $this->key, array(
                'data' => $this->getData()
            ));

            $success = $response->success();
        } else {
            $success = $this->create();
        }

        return $success;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $success = false;
        if (!empty($this->key)) {
            $response = $this->firebase->delete(self::getNodeName() . '/' . $this->key);

            $success = $response->success();
        }

        return $success;
    }

    /**
     * @param $key
     * @param JCFirebase $firebase
     *
     * @return object
     */
    public static function findByKey($key, JCFirebase $firebase)
    {
        $response = $firebase->get(self::getNodeName() . '/' . $key);
        $object = null;
        if ($response->success() && $response->body() != 'null') {
            $object = self::map($response->json(), new static());
            $object->key = $key;
            $object->firebase = $firebase;
        }

        return $object;
    }

    /**
     * @param JCFirebase $firebase
     *
     * @return array(FirebaseModel)
     */
    public static function findAll(JCFirebase $firebase)
    {
        $response = $firebase->get(self::getNodeName());
        $objects = array();

        $jsonObject = json_decode($response->body(), true);
        if ($response->success() && count($jsonObject)) {
            do {
                $object = self::map((object)current($jsonObject), new static());
                $object->key = key($jsonObject);
                $object->firebase = $firebase;
                $objects[] = $object;
            } while (next($jsonObject));
        }

        return $objects;
    }

    protected static function map($object, $instance)
    {
        $mapper = new JsonMapper();
        return $mapper->map($object, $instance);
    }
}