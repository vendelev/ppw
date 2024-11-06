<?php
ini_set('pinba.enabled', true);
ini_set('pinba.server', 'pinba:3002');


const USE_PINBA = false;



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LogLevel;
use Slim\Factory\AppFactory;
use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\StreamHandler;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanKind;

//$script_name = 'script-' . rand(1,9) . '.php';
//pinba_script_name_set($script_name);


require __DIR__ . '/vendor/autoload.php';

require('dice.php');

$logger = new Logger('dice-server');
$logger->pushHandler(new StreamHandler('php://stdout', Level::Info));

$app = AppFactory::create();

$dice = new Dice();

$app->get('/[{anything}]', function (Request $request, Response $response) use ($logger, $dice) {
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


/*
require 'dice.php';
require 'instrumentation.php';

$dice = new Dice();
$result = $dice->roll(10);
var_dump($result);

*/