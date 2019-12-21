<?php

namespace sockball\logistics\base\YTO;

use Exception;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

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
        $response = new Response($waybillNo, self::CODE);
        try {
            $raw = Request::post(self::REQUEST_URL, ['waybillNo' => $waybillNo]);
        } catch (Exception $e) {
            return $response->setError($e->getMessage());
        }

        [$success, $result] = $this->parseRaw($raw);
        if ($success)
        {
            return $response->setSuccess($raw, $result);
        }

        return $response->setFailed($raw, $result);
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
            return [false, $raw->message ?? ''];
        }
    }

    protected function isValid($result)
    {
        return isset($result->code) && $result->code === self::REQUEST_SUCCESS;
    }
}
