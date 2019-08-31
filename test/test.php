<?php

/*
 * 测试所有类型快递的有效性
 */
require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

function analyzeResult($result, $name)
{
    if ($result['code'] === Logistics::RESPONSE_SUCCESS)
    {
        echo "{$name} 正常", PHP_EOL;
    }
    else
    {
        echo "{$name} 异常：{$result['msg']}", PHP_EOL;
    }
}

$logistics = Logistics::getInstance();

// 申通
$waybillNo = '3720159483221';
$result = $logistics->getLatestTrace(Logistics::TYPE_STO, $waybillNo);
analyzeResult($result, '申通快递');

// 圆通
$waybillNo = 'l';
$result = $logistics->getLatestTrace(Logistics::TYPE_YTO, $waybillNo);
analyzeResult($result, '圆通速递');

// 中通
$waybillNo = '75166031906321';
$result = $logistics->getLatestTrace(Logistics::TYPE_ZTO, $waybillNo);
analyzeResult($result, '中通快递');

// 百世快递
$waybillNo = '70577935260961';
$result = $logistics->getLatestTrace(Logistics::TYPE_BEST, $waybillNo);
analyzeResult($result, '百世快递');

// 丹鸟快递
$waybillNo = '611090452344701';
$result = $logistics->getLatestTrace(Logistics::TYPE_DANN, $waybillNo);
analyzeResult($result, '丹鸟快递');

// 中国邮政
$waybillNo = '9860401996391';
$result = $logistics->getLatestTrace(Logistics::TYPE_CHPO, $waybillNo);
analyzeResult($result, '中国邮政');
