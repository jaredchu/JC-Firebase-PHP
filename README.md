<p align="center"><img src="http://i.imgur.com/CTP9Dmu.jpg"></p>
<p align="center">PHP library access Firebase RESTful API</p>

[![Packagist](https://img.shields.io/packagist/v/jaredchu/JC-Firebase-PHP.svg)](https://packagist.org/packages/jaredchu/jc-firebase-php)
[![Travis](https://img.shields.io/travis/jaredchu/JC-Firebase-PHP.svg)](https://travis-ci.org/jaredchu/JC-Firebase-PHP)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/jaredchu/JC-Firebase-PHP.svg)](https://scrutinizer-ci.com/g/jaredchu/JC-Firebase-PHP/)
[![Scrutinizer branch](https://img.shields.io/scrutinizer/coverage/g/jaredchu/JC-Firebase-PHP/master.svg)](https://scrutinizer-ci.com/g/jaredchu/JC-Firebase-PHP/)
[![Packagist](https://img.shields.io/packagist/l/jaredchu/JC-Firebase-PHP.svg)](https://packagist.org/packages/jaredchu/jc-firebase-php)

## Installation
`composer require jaredchu/jc-firebase-php`

## Usage
Create [service account](https://cloud.google.com/iam/docs/service-accounts) to get `json key file`.

```php
use JCFirebase\JCFirebase;
$firebase = new JCFirebase::fromKeyFile( $firebaseURI, $jsonKeyFile );

$response = $firebase->put('user', array('data' => array("first_name"=>"Jared","last_name"=>"Chu")));
echo $response->status_code;
echo $response->body;

$data = $firebase->get();
vardump($data);
```

## Contributing
1. Fork it!
2. Create your feature branch: `git checkout -b feature/your-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/your-new-feature`
5. Submit a pull request.

## License
[MIT License](https://github.com/jaredchu/JC-Firebase-PHP/blob/master/README.md)
