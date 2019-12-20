<?php

/*
 * 测试所有类型快递的有效性
 */
require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;
use \sockball\logistics\lib\Response;
function parse(Response $response, $name)
{
    if ($response->isSuccess())
    {
        echo "{$name} 正常\n";
    }
    else
    {
        $error = $response->getError();
        echo "{$name} 异常：$error\n";
    }
}

$logistics = Logistics::getInstance();

// 申通
$waybillNo = '773017017415724';
$response = $logistics->query(Logistics::TYPE_STO, $waybillNo);
parse($response, '申通快递');

// 圆通
$waybillNo = 'YT4234858984188';
$response = $logistics->query(Logistics::TYPE_YTO, $waybillNo);
parse($response, '圆通速递');

// 中通
$waybillNo = '75166031906321';
$response = $logistics->query(Logistics::TYPE_ZTO, $waybillNo);
parse($response, '中通快递');

// 百世快递
$waybillNo = '70577935260961';
$response = $logistics->query(Logistics::TYPE_BEST, $waybillNo);
parse($response, '百世快递');

// 丹鸟快递
$waybillNo = '611090452344701';
$response = $logistics->query(Logistics::TYPE_DANN, $waybillNo);
parse($response, '丹鸟快递');

// 中国邮政
$waybillNo = '9860401996391';
$response = $logistics->query(Logistics::TYPE_CHPO, $waybillNo, ['python_cli' => 'python']);
parse($response, '中国邮政');
