<?php

namespace sockball\logistics\base;

use stdClass;
use sockball\logistics\common\LogisticsInterface;

class BaseLogistics implements LogisticsInterface
{
    protected const RESPONSE_SUCCESS = 0;
    protected const RESPONSE_FAILED = -1;
    protected $_traces;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {

    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {

    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {

    }

    protected function success(array $data)
    {
        return [
            'code' => self::RESPONSE_SUCCESS,
            'data' => $data,
        ];
    }

    protected function failed(string $msg)
    {
        return [
            'code' => self::RESPONSE_FAILED,
            'msg' => $msg,
        ];
    }

    protected function isResponseFailed($traces)
    {
        return isset($traces['code']) && $traces['code'] === self::RESPONSE_FAILED;
    }

    protected function isRequestSuccess(stdClass $result)
    {
        return true;
    }

    protected function formatTraceInfo(stdClass $trace)
    {

    }
}
