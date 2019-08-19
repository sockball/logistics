<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '75166031906321';

print_r(Logistics::getLatestTrace(Logistics::TYPE_ZTO, $waybillNo));

echo PHP_EOL;

print_r(Logistics::getFullTraces(Logistics::TYPE_ZTO, $waybillNo));
