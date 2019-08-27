<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

// $waybillNo = '71022187451584';
$waybillNo = '70577935260961';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_BEST, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_BEST, $waybillNo));
