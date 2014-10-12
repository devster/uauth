Uauth
=====

[![Build Status](https://travis-ci.org/devster/uauth.svg?branch=master)](https://travis-ci.org/devster/uauth)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/devster/uauth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/devster/uauth/?branch=master)
[![PHP version](https://badge.fury.io/ph/devster%2Fuauth.svg)](http://badge.fury.io/ph/devster%2Fuauth)

Micro PHP HTTP authentication library

![](http://i.imgur.com/gaxDghEl.png)

Installation
------------

    composer require devster/uauth "1.*"

Or require a single file

```php
require_once 'src/Basic.php';

use Uauth\Basic;
```

Usage
-----

### HTTP Basic

#### Simple usecase

```php
require_once 'vendor/autoload.php';

// Here is the most simple usecase
$basic = new \Uauth\Basic("Secured Area", ['john' => 'd0e!', 'jon' => '5n0w']);
$basic->auth();
// All code below is secured by HTTP basic, and you can access the user
echo "Welcome ", $basic->getUser();
```

#### More complex usecase

```php
$basic = new \Uauth\Basic("Secured Area");
$basic
    // Implement your own user verification system.
    // The callable must return true if user is allowed, false if not
    ->verify(function ($username, $password) use ($db) {
        $user = $db->findUser($username);
        return $user->password == $password;
    })
    // this code is executed if the login modal is cancelled
    // or if the user is not verified
    ->deny(function () {
        echo "This text appears because you hit the cancel button";
    })
;
$basic->auth();

echo "Welcome ", $basic->getUser(), ", you password is ", $basic->getPassword();
```

#### Silex integration

```php
$app = new Silex\Application();

$app['allowedUsers'] = ['jon' => '5n0w'];

// Create your silex middleware
$httpBasicAuth = function () use ($app) {
    $basic = new \Uauth\Basic("Secured Silex Area", $app['allowedUsers']);
    $basic->auth();

    // we save the current user
    $app['user'] = $basic->getUser();
};

// And now you can use your middleware where you see fit

// Protect the entire app
$app->before($httpBasicAuth);

// Or protect a collection of routes
$blog = $app['controllers_factory'];
$blog->before($httpBasicAuth);
$blog->get('/', function () {
    return 'Blog home page';
});
$app->mount('/private_blog', $blog);

// Or protect a single route
$app->get('/hello', function () use ($app) {
    return 'Hello '.$app->escape($app['user']);
})->before($httpBasicAuth);

$app->run();
```

Tests
-----

Tests require php >= 5.4 because some functional tests need the php built-in web server.

    php -S localhost:8080 -t tests/server
    vendor/bin/phpunit

License
-------

This project is licensed under the MIT License
