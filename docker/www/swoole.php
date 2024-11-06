<?php 

require 'instrumentation.php';
require 'dice.php';

$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
    $dice = new Dice();

    $response->end(print_r($dice->roll(10), true));
});

$http->start();