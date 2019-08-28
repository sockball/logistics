<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '611090452344701';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_DANN, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_DANN, $waybillNo));
