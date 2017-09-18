<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 1/14/17
 * Time: 2:25 PM
 */

namespace JC\Firebase\Models;

use JC\Firebase\JCFirebase;
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
     * @var array
     */
    public static $maps = [];

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
     * @param \JC\Firebase\JCFirebase $firebase
     */
    public function __construct(JCFirebase $firebase = null)
    {
        $this->firebase = $firebase;
    }

    /**
     * @return string
     */
    public static function getNodeName()
    {
        return static::$nodeName ?: strtolower((new \ReflectionClass(get_called_class()))->getShortName());
    }

    /**
     * @param $nodeName
     */
    public static function setNodeName($nodeName)
    {
        static::$nodeName = $nodeName;
    }

    /**
     * @return array
     */
    public static function getMaps()
    {
        return static::$maps;
    }

    /**
     * @param array $maps
     */
    public static function setMaps($maps)
    {
        static::$maps = $maps;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $object = clone $this;
        unset($object->firebase);
        unset($object->key);

        return static::mapAttributes(get_object_vars($object));
    }

    /**
     * @return bool
     */
    public function create()
    {
        $response = $this->firebase->post(static::getNodeName(), array(
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
            $response = $this->firebase->put(static::getNodeName() . '/' . $this->key, array(
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
            $response = $this->firebase->delete(static::getNodeName() . '/' . $this->key);

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
        $response = $firebase->get(static::getNodeName() . '/' . $key);
        $object = null;
        if ($response->success() && $response->body() != 'null') {
            $object = static::map($response->json(), new static());
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
        $response = $firebase->get(static::getNodeName());
        $objects = array();

        $jsonObject = json_decode($response->body(), true);
        if ($response->success() && count($jsonObject)) {
            do {
                $object = static::map((object)current($jsonObject), new static());
                $object->key = key($jsonObject);
                $object->firebase = $firebase;
                $objects[] = $object;
            } while (next($jsonObject));
        }

        return $objects;
    }

    /**
     * @param $object
     * @param $instance
     * @return object
     */
    protected static function map($object, $instance)
    {
        $mapper = new JsonMapper();
        return $mapper->map((object)static::mapAttributes(get_object_vars($object), false), $instance);
    }

    /**
     * @param array $objectVars
     * @param bool $fromLocal
     * @return array
     */
    protected static function mapAttributes(array $objectVars, $fromLocal = true)
    {
        foreach (static::getMaps() as $localAttr => $DBAttr) {
            if ($fromLocal) {
                $objectVars[$DBAttr] = $objectVars[$localAttr];
                unset($objectVars[$localAttr]);
            } else {
                $objectVars[$localAttr] = $objectVars[$DBAttr];
                unset($objectVars[$DBAttr]);
            }
        }

        return $objectVars;
    }
}