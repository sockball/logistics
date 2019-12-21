<?php

namespace sockball\logistics\base\STO;

use Exception;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

/**
 * 申通快递
 */
class STOLogistics extends BaseLogistics
{
    public const CODE = 'sto';
    protected const REQUEST_URL = 'http://www.sto.cn/Service/LoadTrack';

    private const REQUEST_SUCCESS = true;

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);
        try {
            $raw = Request::post(self::REQUEST_URL, ['billCodes' => $waybillNo]);
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
