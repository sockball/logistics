<?php

namespace sockball\logistics\base\CHPO;

use sockball\logistics\base\BaseLogistics;
use sockball\logistics\common\Request;

/*
 * 中国邮政
 */
class CHPOLogistics extends BaseLogistics
{
    private const STATE_SENDING = '派送';
    private const VERIFY_FAILED_STR = 'no';
    private const RETRY_TIMES = 5;
    private const REQUEST_URL = 'http://yjcx.chinapost.com.cn/qps/showPicture/verify/slideVerifyCheck';

    private $_lastQueryNo;

    public function getLatestTrace(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        return $this->success($this->formatTraceInfo($traces[0]));
    }

    public function getFullTraces(string $waybillNo, bool $force = false)
    {
        $traces = $this->getOriginTraces($waybillNo, $force);
        if ($this->isResponseFailed($traces))
        {
            return $traces;
        }

        $data = [];
        foreach ($traces as $trace)
        {
            $data[] = $this->formatTraceInfo($trace);
        }

        return $this->success($data);
    }

    public function getOriginTraces(string $waybillNo, bool $force = false)
    {
        if ($force === true || $this->_lastQueryNo !== $waybillNo)
        {
            $dir = __DIR__;
            $result = json_decode(exec("python3 {$dir}/getMoveX.py"));
            $params = [
                'uuid' => $result->uuid,
                'text[]' => $waybillNo,
                'moveEnd_X' => $result->moveX,
                // 似乎1代表full, 2代表latest
                'selectType' => 1,
            ];

            // 验证失败则重试，误差大约在5以内...
            $pass = false;
            for ($times = 0; $times < self::RETRY_TIMES; $times++)
            {
                $result = (new Request())->post(self::REQUEST_URL, $params, Request::CONTENT_TYPE_FORM);
                if ($this->isRequestSuccess($result))
                {
                    $pass = true;
                    break;
                }
                else if ($result->YZ === self::VERIFY_FAILED_STR)
                {
                    $params['moveEnd_X']++;
                }
                else
                {
                    break;
                }
            }

            if ($pass)
            {
                if (empty($result))
                {
                    return $this->failed('暂无信息');
                }
                else
                {
                    $this->_traces = $result;
                    $this->_lastQueryNo = $waybillNo;
                }                
            }
            else if (($result->YZ === self::VERIFY_FAILED_STR))
            {
                return $this->failed('指定误差内未能成功通过滑动验证！！！');
            }
            else
            {
                return $this->failed("错误码为{$result->YZ}？？？");
            }
        }

        return $this->_traces;
    }

    /**
     * 
     * YZ可能的值:
     * no：未通过滑动验证
     * unnormal：未提供单号
     * noSession：未正确请求生成uuid
     * 
     * @return bool
     */
    protected function isRequestSuccess($result)
    {
        return !isset($result->YZ);
    }

    protected function formatTraceInfo($trace)
    {
        return [
            'time' => strtotime($trace->opTime),
            'info' => $trace->statusDesc,
            'state' => $trace->opCodeStatus,
        ];
    }
}
