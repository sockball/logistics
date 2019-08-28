<?php

namespace sockball\logistics\base\DANN;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

class DANNLogistics extends BaseLogistics
{
    // private const STATE_SENDING = '';
    private const REQUEST_SUCCESS = true;
    private const REQUEST_FAILED = false;
    private const REQUEST_URL = 'https://portal.danniao.com/logisticsDetails/lpcPackPubQuery';

    private $_lastQueryNo;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        $trace = $this->formatTraceInfo($traces['detail'][0]);
        $trace['state'] = $traces['state'];

        return $this->success($trace);
    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        $data = [];
        foreach ($traces['detail'] as $trace)
        {
            $data[] = $this->formatTraceInfo($trace);
        }
        $data[0]['state'] = $traces['state'];

        return $this->success($data);
    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {
        if ($force === true || $this->_lastQueryNo !== $waybillNo)
        {
            $result = (new Request())->get(self::REQUEST_URL, ['mailNoList' => $waybillNo]);
            if ($this->isRequestSuccess($result))
            {
                $traces = $result->data[0]->fullTraceDetail ?? null;
                if ($traces === null)
                {
                    return $this->failed('暂无信息');
                }
                $this->_traces = [
                    'state' => $result->data[0]->logisticsStatusDesc,
                    'detail' => $traces,
                ];
                $this->_lastQueryNo = $waybillNo;
            }
            else
            {
                return $this->failed($result->message);
            }
        }

        return $this->_traces;
    }

    protected function isRequestSuccess($result)
    {
        return isset($result->success) && $result->success === self::REQUEST_SUCCESS;
    }

    protected function formatTraceInfo($trace)
    {
        return [
            'time' => strtotime($trace->time),
            'info' => $trace->desc,
            'state' => null,
        ];
    }
}
