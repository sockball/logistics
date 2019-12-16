<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = 'YT4234858984188';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_YTO, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_YTO, $waybillNo));
