<?php

require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$waybillNo = '75166031906321';

$logistics = Logistics::getInstance();
print_r($logistics->getLatestTrace(Logistics::TYPE_ZTO, $waybillNo));

echo PHP_EOL;

print_r($logistics->getFullTraces(Logistics::TYPE_ZTO, $waybillNo));
