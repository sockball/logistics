<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;
use sockball\logistics\base\Trace;

$waybillNo = '9860401996391';

$logistics = Logistics::getInstance();
$response = $logistics->query(Logistics::TYPE_CHPO, $waybillNo);

if ($response->isSuccess())
{
    foreach ($response as $trace)
    {
        /** @var Trace $trace */
        // echo $trace->timestamp;
        // echo $trace->state;
        echo $trace->info . "\n";
    }
    // print_r($response->getLatest());
    // print_r($response->getAll());
    // print_r($response->getRaw());
}
else
{
    echo $response->getError();
}
