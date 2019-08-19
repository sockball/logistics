<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '3720159483221';

print_r(Logistics::getLatestTrace(Logistics::TYPE_STO, $waybillNo));

echo PHP_EOL;

print_r(Logistics::getFullTraces(Logistics::TYPE_STO, $waybillNo));
