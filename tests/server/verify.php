<?php

require_once __DIR__.'/../../vendor/autoload.php';

$basic = new \Uauth\Basic;
$basic
    ->realm('Bob zone')
    ->verify(function ($user, $pass) {
        return 'bob' == $user && 'bobby' == $pass;
    })
    ->deny(function () {
        echo "Unauthorized";
    })
    ->auth()
;

echo "Welcome ", $basic->getUser();
