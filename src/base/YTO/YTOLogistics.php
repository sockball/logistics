<?php

namespace sockball\logistics\base\YTO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

class YTOLogistics extends BaseLogistics
{
    private const STATE_SENDING = '派送';
    private const REQUEST_SUCCESS = 'success';
    private const REQUEST_FAILED = 'failed';
    private const REQUEST_URL = 'http://www.yto.net.cn/api/trace/waybill';

    private $_lastQueryNo;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        return $this->success($this->formatTraceInfo($traces[0]));
    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        $data = [];
        foreach ($traces as $trace)
        {
            $data[] = $this->formatTraceInfo($trace);
        }

        return $this->success($data);
    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {
        if ($force === true || $this->_lastQueryNo !== $waybillNo)
        {
            $result = (new Request())->post(self::REQUEST_URL, ['waybillNo' => $waybillNo], Request::CONTENT_TYPE_FORM);
            if ($this->isRequestSuccess($result))
            {
                $this->_traces = $result->data[0]->traces ?? null;
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
        return isset($result->code) && $result->code === self::REQUEST_SUCCESS;
    }

    protected function formatTraceInfo($trace)
    {
        return [
            'time' => $trace->time / 1000,
            'info' => $trace->info,
            'state' => $trace->type,
        ];
    }
}
