<?php

namespace sockball\logistics\base\ZTO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

class ZTOLogistics extends BaseLogistics
{
    private const STATE_SENDING = '派件';
    private const REQUEST_SUCCESS = true;
    private const REQUEST_FAILED = false;
    private const REQUEST_URL = 'https://hdgateway.zto.com/WayBill_GetDetail';

    private $_lastQueryNo;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        return $this->success($this->formatTraceInfo($traces[0][0]));
    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        $data = [];
        foreach ($traces as $dayTraces)
        {
            foreach ($dayTraces as $trace)
            {
                $data[] = $this->formatTraceInfo($trace);
            }
        }

        return $this->success($data);
    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {
        if ($force === true || $this->_lastQueryNo !== $waybillNo)
        {
            $result = (new Request())->post(self::REQUEST_URL, ['billCode' => $waybillNo], Request::CONTENT_TYPE_FORM);
            if ($this->isRequestSuccess($result))
            {
                $this->_traces = $result->result->logisticsRecord ?? null;
                if ($this->_traces === null)
                {
                    return $this->failed('暂无信息');
                }
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
        return isset($result->status) && $result->status === self::REQUEST_SUCCESS;
    }

    protected function formatTraceInfo($trace)
    {
        return [
            'time' => strtotime($trace->scanDate),
            'info' => $trace->stateDescription,
            'state' => $trace->scanType,
        ];
    }
}
