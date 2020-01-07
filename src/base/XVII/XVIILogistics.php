<?php

namespace sockball\logistics\base\XVII;

use Exception;
use RuntimeException;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

/*
 * 17track
 */
class XVIILogistics extends BaseLogistics
{
    public const CODE = '17track';
    protected const REQUEST_URL = 'https://t.17track.net/restapi/track';

    private const REQUEST_SUCCESS = 1;

    public function beforeQuery()
    {
        if (!function_exists('exec'))
        {
            throw new RuntimeException('17track replies on exec and python!!');
        }
    }

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);

        try {
            [$success, $raw] = $this->request($waybillNo, $options['python_cli'] ?? 'python');
        } catch (Exception $e) {
            return $response->setError($e->getMessage());
        }

        if ($success)
        {
            if ($raw->dat[0]->track->z0 !== null)
            {
                return $response->setSuccess($raw, $this->parseRaw($raw));
            }
    
            return $response->setFailed($raw, '暂无信息');
        }

        return $response->setError($raw);
    }

    private function request(string $waybillNo, string $python)
    {
        $dir = __DIR__;
        $raw = null;
        $cliError = [];

        $lastEventID = exec("{$python} {$dir}/getLastEventID.py -c {$waybillNo} 2>&1", $cliError);
        if (count($cliError) > 1)
        {
            // possible error...
            throw new Exception(implode('', $cliError));
        }

        $params = json_encode([
            'data' => [
                    [
                        'num' => $waybillNo,
                        'fc' => 0,
                        'sc' => 0,
                    ],
            ],
            'guid' => '',
            // 时区有无影响?
            'timeZoneOffset' => -480,
        ]);
        while (true)
        {
            $cookie = Request::createCookie(['Last-Event-ID' => $lastEventID], '.17track.net');
            $raw = Request::post(self::REQUEST_URL, [], true, [
                'body' => $params,
                'cookies' => $cookie,
            ]);

            if ($this->isValid($raw))
            {
                if ($raw->dat[0]->delay !== 0)
                {
                    // delay
                    sleep(1);
                    continue;
                }
                return [true, $raw];
            }
            return [false, $raw->msg];
        }
    }

    protected function isValid($result)
    {
        return $result->ret === self::REQUEST_SUCCESS;
    }

    protected function parseRaw($raw)
    {
        $traces = [];
        $keys = array_keys((array) $raw->dat[0]->track);
        rsort($keys);
        foreach ($keys as $key)
        {
            // find the last language...
            if (preg_match('/z\d+/', $key) && !empty($raw->dat[0]->track->$key))
            {
                break;
            }
        }

        foreach ($raw->dat[0]->track->$key as $item)
        {
            $traces[] = new Trace(strtotime($item->a), "【{$item->c}: {$item->z}", null);
        }

        return $traces;
    }
}
