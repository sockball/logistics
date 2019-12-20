<?php

namespace sockball\logistics\base\YTO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;

/**
 * 圆通速递
 */
class YTOLogistics extends BaseLogistics
{
    public const CODE = 'yto';
    protected const REQUEST_URL = 'http://www.yto.net.cn/api/trace/waybill';

    private const REQUEST_SUCCESS = 'success';

    public function query(string $waybillNo, array $options = [])
    {
        $response = Request::post(self::REQUEST_URL, ['waybillNo' => $waybillNo]);
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

    protected function isValid($result)
    {
        return isset($result->code) && $result->code === self::REQUEST_SUCCESS;
    }

    protected function parseRaw($raw)
    {
        if ($this->isValid($raw))
        {
            $result = $raw->data[0]->traces ?? null;
            if ($result === null)
            {
                return [false, '暂无信息'];
            }
            $traces = [];
            foreach ($result as $item)
            {
                $traces[] = new Trace($item->time / 1000, $item->info, $item->type);
            }

            return [true, $traces];
        }
        else
        {
            return [false, $result->message ?? ''];
        }
    }
}
