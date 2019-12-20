<?php

namespace sockball\logistics\base\STO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;

class STOLogistics extends BaseLogistics
{
    public const CODE = 'sto';
    protected const REQUEST_URL = 'http://www.sto.cn/Service/LoadTrack';

    private const REQUEST_SUCCESS = true;

    public function query(string $waybillNo, array $options = [])
    {
        $response = Request::post(self::REQUEST_URL, ['billCodes' => $waybillNo]);
        $result = null;
        if ($response->isSuccess())
        {
            [$success, $result] = $this->parseRaw($response->getRaw());
            if ($success)
            {
                return $response->setSuccess($waybillNo, self::CODE, $result);
            }
        }

        return $response->setFailed($waybillNo, self::CODE, $result);
    }

    protected function parseRaw($raw)
    {
        if ($this->isValid($raw))
        {
            $result = json_decode($raw->ResultValue)[0]->ScanList ?? null;
            if (empty($result))
            {
                return [false, '暂无信息'];
            }
            $traces = [];
            foreach ($result as $item)
            {
                $traces[] = new Trace(strtotime($item->ScanDate), $item->Memo, $item->ScanType);
            }

            return [true, $traces];
        }
        else
        {
            return [false, $raw->StatusMessage ?? ''];
        }
    }

    protected function isValid($result)
    {
        return isset($result->Status) && $result->Status === self::REQUEST_SUCCESS;
    }
}
