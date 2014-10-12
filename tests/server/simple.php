<?php

require_once __DIR__.'/../../vendor/autoload.php';

$basic = new \Uauth\Basic("My restricted Area", ['jon' => 'snow']);
$basic->auth();

echo "Welcome ", $basic->getUser();
