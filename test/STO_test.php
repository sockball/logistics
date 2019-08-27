<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '3720159483221';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_STO, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_STO, $waybillNo));
