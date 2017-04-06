<p align="center"><img src="http://i.imgur.com/CTP9Dmu.jpg"></p>
<h1 align="center">PHP library access Firebase RESTful API</h1>

[![Packagist](https://img.shields.io/packagist/v/jaredchu/JC-Firebase-PHP.svg)](https://packagist.org/packages/jaredchu/jc-firebase-php)
[![Packagist](https://img.shields.io/packagist/dt/jaredchu/JC-Firebase-PHP.svg)](https://packagist.org/packages/jaredchu/jc-firebase-php)
[![Travis](https://img.shields.io/travis/jaredchu/JC-Firebase-PHP.svg)](https://travis-ci.org/jaredchu/JC-Firebase-PHP)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/jaredchu/JC-Firebase-PHP.svg)](https://scrutinizer-ci.com/g/jaredchu/JC-Firebase-PHP/)
[![Scrutinizer branch](https://img.shields.io/scrutinizer/coverage/g/jaredchu/JC-Firebase-PHP/master.svg)](https://scrutinizer-ci.com/g/jaredchu/JC-Firebase-PHP/)
[![Packagist](https://img.shields.io/packagist/l/jaredchu/JC-Firebase-PHP.svg)](https://packagist.org/packages/jaredchu/jc-firebase-php)

## Installation
`$ composer require jaredchu/jc-firebase-php`

## Simple usage
Generate a [private key in JSON format](https://cloud.google.com/storage/docs/authentication#service_accounts).
<img src="http://i.imgur.com/d4pBQqB.png">

#### Check Firebase credential
```php
use JCFirebase\JCFirebase;
$firebase = new JCFirebase::fromKeyFile( $firebaseURI, $jsonKeyFile );
if( $firebase->isValid() ){
    //do something
}
```

#### GET - Reading Data
```php
$response = $firebase->get('user');
echo $response->success;
echo $response->body;
```
#### PUT - Writing Data
```php
$response = $firebase->put('user', array('data' => array('first_name'=>'Jared','last_name'=>'Chu')));
echo $response->status_code;
echo $response->body;
```

#### POST - Pushing Data
```php
$response = $firebase->post('log', array('data' => array('code'=>401,'message'=>'Not Authorized')));
echo $response->status_code;
echo $response->body;
```
#### PATCH - Updating Data
```php
$response = $firebase->patch('user', array('data' => array('first_name'=>'Jared',
                             'last_name'=>'Leto','age'=>27)));
echo $response->status_code;
echo $response->body;
```
#### DELETE - Removing Data
```php
$response = $firebase->delete('user/first_name');
echo $response->status_code;
echo $response->body;
```
## Modeling

#### Create Firebase connector
```php
use JCFirebase\JCFirebase;
$firebase = new JCFirebase::fromKeyFile( $firebaseURI, $jsonKeyFile );
```
#### Extend your Model with FirebaseModel
```php
class Log extends FirebaseModel {
	/**
	 * @var integer
	 */
	public $code;
	/**
	 * @var string
	 */
	public $message;
}
```
#### Get record
```php
$log = Log::findByKey( $key, $firebase );
echo $log->key;
echo $log->code;
echo $log->message;

$logs = Log::findAll( $firebase );
foreach ($logs as $log){
    echo $log->key;
    echo $log->code;
    echo $log->message;
}
```

#### Create record
```php
$log          = new Log( $firebase );
$log->code    = 200;
$log->message = 'Success';
$log->save();
```

#### Update record
```php
$log = Log::findByKey( $key, $firebase );
$log->code    = 400;
$log->message = 'Bad Request';
$log->save();
```
#### Delete record
```php
$log = Log::findByKey( $key, $firebase );
$log->delete();
```
## Contributing
1. Fork it!
2. Create your feature branch: `$ git checkout -b feature/your-new-feature`
3. Commit your changes: `$ git commit -am 'Add some feature'`
4. Push to the branch: `$ git push origin feature/your-new-feature`
5. Submit a pull request.

## License
[MIT License](https://github.com/jaredchu/JC-Firebase-PHP/blob/master/README.md)
