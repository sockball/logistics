<?php

/**
 * 寻找有效的快递单号...
 */
require_once __DIR__ . '/../vendor/autoload.php';

use sockball\logistics\Logistics;

$prefix = 'YT';
$suffix = '';
$validWaybillNo = '4234858984188';
$type = Logistics::TYPE_YTO;
$logistics = new Logistics();

/**
 * 根据有效单号(数字)随机生成单号
 *
 * @param  string $validWaybillNo 有效单号(仅包含数字部分)
 * @return string
 */
function getRandomWaybillNo(string $validWaybillNo)
{
    $length = strlen($validWaybillNo);
    // 随机取有效单号的前几位数字
    $randomHeadLen = mt_rand(1, 5);
    // 一次随机6位数 100000 - 999999
    $digit = 6;
    $rest = $length - $randomHeadLen;
    $randomHead = substr($validWaybillNo, 0, $randomHeadLen);

    $quotient = floor($rest / $digit);
    $remainder = $rest % $digit;

    $append = '';
    for ($i = 1; $i <= $quotient; $i++)
    {
        $append .= mt_rand(10 ** ($digit - 1), str_repeat(9, $digit));
    }

    // 余数位
    if ($remainder != 0)
    {
        $append .= mt_rand(10 ** ($remainder - 1), str_repeat(9, $remainder));
    }

    return $randomHead . $append;
}

while (true)
{
    $waybillNo = $prefix . getRandomWaybillNo($validWaybillNo) . $suffix;
    $response = $logistics->query($type, $waybillNo);
    if ($response->isSuccess())
    {
        echo "{$waybillNo}\n{$response->info}\n";
        break;
    }
    else if ($response->isFailed())
    {
        echo "{$waybillNo} failed\n{$response->getMsg()}\n";
    }
    else
    {
        echo "{$waybillNo} error\n{$response->getError()}\n";
    }
    // 防请求频率限制 比如圆通使用openresty...
    sleep(1);
}
