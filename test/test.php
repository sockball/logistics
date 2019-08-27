<?php

/*
 * 测试所有类型快递的有效性
 */
require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

function isSuccess($result)
{
    return $result['code'] === Logistics::RESPONSE_SUCCESS;
}

$logistics = Logistics::getInstance();

// 申通
$waybillNo = '3720159483221';
$result = $logistics->getLatestTrace(Logistics::TYPE_STO, $waybillNo);
echo isSuccess($result) ? '申通快递 正常' : '申通快递 异常', PHP_EOL;

// 圆通
$waybillNo = 'l';
$result = $logistics->getLatestTrace(Logistics::TYPE_YTO, $waybillNo);
echo isSuccess($result) ? '圆通速递 正常' : '圆通速递 异常', PHP_EOL;

// 中通
$waybillNo = '75166031906321';
$result = $logistics->getLatestTrace(Logistics::TYPE_ZTO, $waybillNo);
echo isSuccess($result) ? '中通快递 正常' : '中通快递 异常', PHP_EOL;

// 百世快递
$waybillNo = '70577935260961';
$result = $logistics->getLatestTrace(Logistics::TYPE_BEST, $waybillNo);
echo isSuccess($result) ? '百世快递 正常' : '百世快递 异常', PHP_EOL;
