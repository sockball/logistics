<?php

namespace sockball\logistics\base\DANN;

use Exception;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

/*
 * 丹鸟快递
 */
class DANNLogistics extends BaseLogistics
{
    public const CODE = 'dan_niao';
    protected const REQUEST_URL = 'https://portal.danniao.com/logisticsDetails/lpcPackPubQuery';

    private const REQUEST_SUCCESS = true;

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);
        try {
            $raw = Request::get(self::REQUEST_URL, ['mailNoList' => $waybillNo]);
        } catch (Exception $e) {
            return $response->setError($e->getMessage());
        }

        [$success, $result] = $this->parseRaw($raw);
        if ($success)
        {
            return $response->setSuccess($result);
        }

        return $response->setFailed($result);
    }

    protected function parseRaw($raw)
    {
        if ($this->isValid($raw))
        {
            $result = $raw->data[0]->fullTraceDetail ?? null;
            if ($result === null)
            {
                return [false, '暂无信息'];
            }
            // 仅有最新状态
            $latestState = $raw->data[0]->logisticsStatusDesc;
            $traces = [];
            foreach ($result as $index => $item)
            {
                $state = ($index === 0) ? $latestState : null;
                $traces[] = new Trace(strtotime($item->time), $item->desc, $state);
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
        return isset($result->success) && $result->success === self::REQUEST_SUCCESS;
    }
}
