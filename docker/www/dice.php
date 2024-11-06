<?php

use OpenTelemetry\API\Globals;
use GuzzleHttp\Client;



class Dice {

    public $tracer;
    const USE_OTEL = false;

    function __construct() {
        if (!self::USE_OTEL) {
            return;
        }
        $tracerProvider = Globals::tracerProvider();

        $this->tracer = $tracerProvider->getTracer(
          'dice-lib',
          '0.1.0',
          'https://opentelemetry.io/schemas/1.24.0'
        );
    }

    public function roll($rolls) {
        if (self::USE_OTEL) {
            $span = $this->tracer->spanBuilder("rollTheDice")->startSpan();
            $scope = $span->activate();
        }
        $result = [];
        for ($i = 0; $i < $rolls; $i++) {
            $result[] = $this->rollOnce();
        }
        if (self::USE_OTEL) {
            $span->end();
            $scope->detach();
        }
        return $result;
    }

    private function rollOnce() {
        if (self::USE_OTEL) {
            $span = $this->tracer->spanBuilder("curlRequest")->startSpan();
        }
        if (USE_PINBA) {
            $timer = pinba_timer_start(['block'=>'block-' . rand(0,5)]);
        }

        $client = new Client();
        $res = $client->request('GET', 'http://echo-server:8088/payload?io_msec=1');
        $result = random_int(1, 6);
        if (self::USE_OTEL) {
            $span->end();
        }

        if (USE_PINBA) {
            pinba_timer_stop($timer);
        }
        return $result;
    }
}