<?php

namespace sockball\logistics\base\BEST;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

/**
 * 百世快递
 */
class BESTLogistics extends BaseLogistics
{
    private const STATE_SENDING = '派件';
    private const REQUEST_URL = 'http://www.800bestex.com/Bill/Track';

    private $_lastQueryNo;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        return $this->success($traces[0]);
    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        return $this->success($traces);
    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {
        if ($force === true || $this->_lastQueryNo !== $waybillNo)
        {
            $result = (new Request())->post(self::REQUEST_URL, ['code' => $waybillNo], Request::CONTENT_TYPE_FORM, false);
            $rawPattern = '/<tr[\s\S]*?>[\s\S]*?<\/tr>/';
            preg_match_all($rawPattern, $result, $raw);
            if ($this->isRequestSuccess($raw))
            {
                array_shift($raw[0]);
                $traces = [];
                $statePattern = '/data-type="(.*?)"/';
                $infoPattern = '/<td>(.*?)<\/td>/';
                foreach ($raw[0] as $v)
                {
                    preg_match($statePattern, $v, $state);
                    preg_match_all($infoPattern, $v, $info);

                    if (empty($state))
                    {
                        // <td colspan="3">暂时没有该运单的追踪记录，请随时关注订单动态</td>
                        return $this->failed('暂无信息');
                    }
                    $traces[] = [
                        'time' => strtotime($info[1][0]),
                        'info' => strip_tags($info[1][2]),
                        'state' => $state[1],
                    ];
                }
                $this->_lastQueryNo = $waybillNo;
                $this->_traces = $traces;
            }
            else
            {
                return $this->failed('没有这样的单号');
            }
        }

        return $this->_traces;
    }

    protected function isRequestSuccess($result)
    {
        return count($result[0] ?? []) > 1;
    }

    protected function formatTraceInfo($trace)
    {

    }
}
