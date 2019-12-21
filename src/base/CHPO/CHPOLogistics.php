<?php

namespace sockball\logistics\base\CHPO;

use Exception;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

/*
 * 中国邮政
 */
class CHPOLogistics extends BaseLogistics
{
    public const CODE = 'china_post';
    protected const REQUEST_URL = 'http://yjcx.chinapost.com.cn/qps/showPicture/verify/slideVerifyCheck';

    private const SLIDE_VERIFY_FAILED = 'no';
    private const RETRY_TIMES = 5;

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);

        $this->checkRequire();

        try {
            [$slideVerify, $raw, $cliError] = $this->request($waybillNo, $options['python_cli'] ?? 'python');
        } catch (Exception $e) {
            return $response->setError($e->getMessage());
        }

        if ($slideVerify)
        {
            if (empty($raw))
            {
                return $response->setFailed('暂无信息');
            }
            else
            {
                return $response->setSuccess($this->parseRaw($raw));
            }
        }
        else if (!isset($raw->YZ))
        {
            // cli error
            $error = implode('', $cliError);
        }
        else if ($raw->YZ === self::SLIDE_VERIFY_FAILED)
        {
            $error = '指定重试次数内未能成功通过滑动验证！！！';
        }
        else
        {
            $error = "错误码为{$raw->YZ}？？？";
        }

        return $response->setError($error);
    }

    private function checkRequire()
    {
        if (!function_exists('exec'))
        {
            throw new Exception('function exec required!');
        }
    }

    private function request($waybillNo, $python)
    {
        // 滑动验证失败则重试...
        $slideVerify = false;
        $dir = __DIR__;
        $raw = null;

        for ($times = 0; $times < self::RETRY_TIMES; $times++)
        {
            $cliError = [];
            $cliResult = json_decode(exec("{$python} {$dir}/getMoveX.py 2>&1", $cliError));
            if (!isset($cliResult->uuid))
            {
                continue;
            }
            $params = [
                'uuid' => $cliResult->uuid,
                'text[]' => $waybillNo,
                'moveEnd_X' => $cliResult->moveX,
                // 似乎1代表full, 2代表latest
                'selectType' => 1,
            ];
            $raw = Request::post(self::REQUEST_URL, $params);
            if ($this->isValid($raw))
            {
                $slideVerify = true;
                break;
            }
            else if ($raw->YZ === self::SLIDE_VERIFY_FAILED)
            {
                continue;
            }
            else
            {
                break;
            }
        }

        return [$slideVerify, $raw, $cliError];
    }

    /**
     * YZ可能的值:
     * no：未通过滑动验证，一个uuid只能验证一次，请求一次即失效
     * unnormal：未提供单号
     * noSession：未正确请求生成uuid
     *
     * @param \stdClass $result
     * @return bool
     */
    protected function isValid($result)
    {
        return !isset($result->YZ);
    }

    protected function parseRaw($raw)
    {
        $traces = [];
        foreach ($raw as $item)
        {
            $traces[] = new Trace(strtotime($item->opTime), $item->statusDesc, $item->opCodeStatus);
        }

        return $traces;
    }
}
