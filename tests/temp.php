<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 2020-02-16
 * Time: 11:23
 */

const B64_KEY_FILE = __DIR__ . '/../resource/encoded_json_file.b64';
$file = file_get_contents(B64_KEY_FILE);
var_dump($file);
var_dump(base64_decode($file));
