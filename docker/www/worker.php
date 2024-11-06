<?php

const USE_PINBA = true;

include "vendor/autoload.php";

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\SDK\Common\Log\LoggerHolder;
use Psr\Log\LogLevel;
use Spiral\RoadRunner;
use Nyholm\Psr7;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;

ini_set('pinba.enabled', true);
ini_set('pinba.server', 'pinba:3002');

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$tracerProvider = (new TracerProviderFactory())->create();
$tracer = $tracerProvider->getTracer('example');

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while ($req = $worker->waitRequest()) {
    try {
        if (USE_PINBA) {
            pinba_reset();
            pinba_script_name_set('worker.php');
        }

        $parent = TraceContextPropagator::getInstance()->extract($req->getHeaders());
        $rootSpan = $tracer
            ->spanBuilder('root')
            ->setParent($parent)
            ->startSpan();
        $scope = $rootSpan->activate();
        try {
            $childSpan = $tracer
                ->spanBuilder('child')
                ->startSpan();
            $rsp = new Psr7\Response();
            for ($i = 0 ; $i < 5 ; $i++) {
                if (USE_PINBA) {
                    $timer = pinba_timer_start(['block'=>'block-' . $i]);
                }
                //$arr = range(0,100000);
                //rsort($arr);
                //usleep(1000);
                if (USE_PINBA) {
                    pinba_timer_stop($timer);
                }

            }

            $rsp->getBody()->write("Job's done!\n");

    
            $worker->respond($rsp);
            $childSpan->end();
            $rootSpan->end();
        } finally {
            //detach scope, clearing state for next request
            $scope->detach();
            if (USE_PINBA) {                
                //pinba_flush(null, PINBA_FLUSH_RESET_DATA);
                pinba_flush();
            }
        }
    } catch (\Throwable $e) {
        $worker->getWorker()->error((string)$e);
    }
}