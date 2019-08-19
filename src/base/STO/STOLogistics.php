<?php

namespace sockball\logistics\base\STO;

use stdClass;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

class STOLogistics extends BaseLogistics
{
    private const TYPE_SENDING = '派送中';
    private const REQUEST_SUCCESS = true;
    private const REQUEST_FAILED = false;
    private const REQUEST_URL = 'http://www.sto.cn/Service/LoadTrack';

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
            $this->_lastQueryNo = $waybillNo;
            $result = (new Request())->post(self::REQUEST_URL, ['billCodes' => $waybillNo], Request::CONTENT_TYPE_FORM);
            if ($this->isRequestSuccess($result))
            {
                $this->_traces = json_decode($result->ResultValue)[0]->ScanList ?? null;
                if ($this->_traces === null)
                {
                    return $this->failed('暂无信息');
                }
            }
            else
            {
                return $this->failed($result->message);
            }
        }

        return $this->_traces;
    }

    protected function isRequestSuccess(stdClass $result)
    {
        return isset($result->Status) && $result->Status === self::REQUEST_SUCCESS;
    }

    protected function formatTraceInfo(stdClass $trace)
    {
        return [
            'time' => strtotime($trace->ScanDate),
            'info' => $trace->Memo,
            'type' => $trace->ScanType,
        ];
    }
}
