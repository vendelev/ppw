<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;


require __DIR__ . '/../vendor/autoload.php';

require('dice.php');

$logger = new Logger('dice-server');
$logger->pushHandler(new StreamHandler('php://stdout', Level::Info));

$app = AppFactory::create();

$dice = new Dice();

$app->get('/{version}/[{anything}]', function (Request $request, Response $response) use ($logger, $dice) {
    $params = $request->getQueryParams();

    if(isset($params['rolls'])) {
        $result = $dice->roll($params['rolls']);
        $response->getBody()->write(json_encode($result));
    } else {
        $response->withStatus(400)->getBody()->write("Please enter a number of rolls");
    }

    return $response;
});

$app->run();
