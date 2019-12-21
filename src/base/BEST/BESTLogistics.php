<?php

namespace sockball\logistics\base\BEST;

use Exception;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\lib\Request;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Response;

/**
 * 百世快递
 */
class BESTLogistics extends BaseLogistics
{
    public const CODE = 'best';
    protected const REQUEST_URL = 'http://www.800bestex.com/Bill/Track';

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);
        try {
            $raw = Request::post(self::REQUEST_URL, ['code' => $waybillNo], false);
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
        $matchPattern = '#<tr[\s\S]*?>[\s\S]*?</tr>#';
        preg_match_all($matchPattern, $raw, $match);
        $result = $match[0];
        if ($this->isValid($result))
        {
            array_shift($result);
            $traces = [];
            $statePattern = '/data-type="(.*?)"/';
            $infoPattern = '#<td>(.*?)</td>#';
            foreach ($result as $v)
            {
                preg_match($statePattern, $v, $state);
                preg_match_all($infoPattern, $v, $info);

                if (empty($state))
                {
                    // <td colspan="3">暂时没有该运单的追踪记录，请随时关注订单动态</td>
                    return [false, '暂无信息'];
                }
                $traces[] = new Trace(strtotime($info[1][0]), strip_tags($info[1][2]), $state[1]);
            }

            return [true, $traces];
        }
        else
        {
            return [false, '没有这样的单号'];
        }
    }

    protected function isValid($result)
    {
        return count($result ?? []) > 1;
    }
}
