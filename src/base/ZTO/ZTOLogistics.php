<?php

namespace sockball\logistics\base\ZTO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;

/**
 * 中通快递
 */
class ZTOLogistics extends BaseLogistics
{
    public const CODE = 'zto';
    protected const REQUEST_URL = 'https://hdgateway.zto.com/WayBill_GetDetail';

    private const REQUEST_SUCCESS = true;

    public function query(string $waybillNo, array $options = [])
    {
        $response = Request::post(self::REQUEST_URL, ['billCode' => $waybillNo]);
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
            $result = $raw->result->logisticsRecord ?? null;
            if ($result === null)
            {
                return [false, '暂无信息'];
            }
            $traces = [];
            foreach ($result as $dayTraces)
            {
                foreach ($dayTraces as $item)
                {
                    $traces[] = new Trace(strtotime($item->scanDate), $item->stateDescription, $item->scanType);
                }
            }

            return [true, $traces];
        }
        else
        {
            return [false, $raw->message ?? ''];
        }
    }

    protected function isValid($result)
    {
        return isset($result->status) && $result->status === self::REQUEST_SUCCESS;
    }
}
