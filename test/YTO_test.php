<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = 'l';

print_r(Logistics::getLatestTrace(Logistics::TYPE_YTO, $waybillNo));

echo PHP_EOL;

print_r(Logistics::getFullTraces(Logistics::TYPE_YTO, $waybillNo));
