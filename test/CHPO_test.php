<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '9860401996391';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_CHPO, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_CHPO, $waybillNo));
