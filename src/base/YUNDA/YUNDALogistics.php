<?php

namespace sockball\logistics\base\YUNDA;

use Exception;
use RuntimeException;
use sockball\logistics\base\BaseLogistics;
use sockball\logistics\base\Trace;
use sockball\logistics\lib\Request;
use sockball\logistics\lib\Response;

/**
 * 韵达
 *
 * @see https://blog.csdn.net/ugg/article/details/3972368
 */
class YUNDALogistics extends BaseLogistics
{
    public const CODE = 'yunda';

    protected const VERIFY_URL = 'http://ykjcx.yundasys.com/zb1qBpg2.php';

    private static $mappings = [
        '0' => '00011000001111000110011011000011110000111100001111000011011001100011110000011000',
        '1' => '00011000001110000111100000011000000110000001100000011000000110000001100001111110',
        '2' => '00111100011001101100001100000011000001100000110000011000001100000110000011111111',
        '3' => '01111100110001100000001100000110000111000000011000000011000000111100011001111100',
        '4' => '00000110000011100001111000110110011001101100011011111111000001100000011000000110',
        '5' => '11111110110000001100000011011100111001100000001100000011110000110110011000111100',
        '6' => '00111100011001101100001011000000110111001110011011000011110000110110011000111100',
        '7' => '11111111000000110000001100000110000011000001100000110000011000001100000011000000',
        '8' => '00111100011001101100001101100110001111000110011011000011110000110110011000111100',
        '9' => '00111100011001101100001111000011011001110011101100000011010000110110011000111100',
        '*' => '00000000000000000000000001101100001110001111111000111000011011000000000000000000',
        '+' => '00000000000000000001100000011000000110001111111100011000000110000001100000000000',
        '=' => '00000000000000000000000000000000111111110000000000000000111111110000000000000000',
    ];

    public function beforeQuery()
    {
        if (!function_exists('exec'))
        {
            throw new RuntimeException('YUNDA replies on exec and python!!');
        }
    }

    public function query(string $waybillNo, array $options = [])
    {
        $response = new Response($waybillNo, self::CODE);
        try {
            [$cookie, $img_str] = Request::getCookie(self::VERIFY_URL);
            $verifyCode = $this->getVerifyResult($img_str);
            $dir = __DIR__;
            $python = $options['python_cli'] ?? 'python';
            $raw = exec("{$python} {$dir}/getResult.py -c {$cookie} -v {$verifyCode} -w {$waybillNo} 2>&1", $cliError);
        } catch (Exception $e) {
            return $response->setError($e->getMessage());
        }

        if (count($cliError) > 1)
        {
            return $response->setError(implode('', $cliError));
        }

        $raw = json_decode($raw);
        if ($this->isValid($raw))
        {
            [$success, $result] = $this->parseRaw($raw->data);
            if ($success)
            {
                return $response->setSuccess($raw->raw, $result);
            }
            else
            {
                return $response->setFailed($raw->raw, $result);
            }
        }
        else
        {
            return $response->setError($raw->msg);
        }
    }

    protected function parseRaw($raw)
    {
        if (is_string($raw))
        {
            return [false, $raw];
        }
        array_pop($raw);
        rsort($raw);
        $traces = [];
        foreach ($raw as $item)
        {
            [$datetime, $info] = explode(',', $item, 2);
            $traces[] = new Trace(strtotime($datetime), strip_tags($info), null);
        }

        return empty($traces) ? [false, '暂无信息'] : [true, $traces];
    }

    protected function isValid($result)
    {
        return $result->success === true;
    }

    private function getVerifyResult(string $img_str)
    {
        $hex = $this->getHex($img_str);
        $nums = $this->getNum($hex);
        [$left, $operator, $right] = $this->match($nums);

        if ($operator === '*')
        {
            return $left * $right;
        }
        else if ($operator === '+')
        {
            return $left + $right;
        }
        else
        {
            throw new Exception("verify code operator error：{$left} {$operator} {$right}");
        }
    }

    /**
     * 验证码图片二值化
     *
     * @param string $img 验证码图片
     * @return array
     */
    private function getHex(string $img)
    {
        $res = imagecreatefromstring($img);
        [$width, $height] = getimagesizefromstring($img);

        $data = [];
        for($i = 0; $i < $height; ++$i)
        {
            for($j = 0; $j < $width; ++$j)
            {
                $color = imagecolorat($res, $j, $i);
                ['red' => $red, 'green' => $green, 'blue' => $blue] = imagecolorsforindex($res, $color);
                $data[$i][$j] = ($red > 200 || $green > 200 || $blue > 200) ? 1 : 0;
            }
        }

        return $data;
    }

    /**
     * 根据二值化后的结果找出对应的数字特征码
     *
     * @param array $hex
     * @return array
     */
    private function getNum(array $hex)
    {
        $data = ['', '', '', ''];
        $wordWith = 8;
        $wordHeight = 10;
        $offsetX = 4;
        $offsetY = 6;
        $wordSpacing = 1;

        for($i = 0; $i < 4; ++$i)
        {
            $x = ($i * ($wordWith + $wordSpacing)) + $offsetX;
            for($h = $offsetY; $h < ($offsetY + $wordHeight); ++$h)
            {
                for($w = $x; $w < ($x + $wordWith); ++$w)
                {
                    $data[$i] .= $hex[$h][$w];
                }
            }
        }

        return $data;
    }

    /**
     * 根据特征码表匹配对应的数字
     *
     * @param array $data
     * @return array
     */
    private function match(array $data)
    {
        $result = [];
        foreach($data as $numKey => $numString)
        {
            $max = 0.0;
            $num = 0;
            foreach(self::$mappings as $key => $value)
            {
                $percent = 0.0;
                similar_text($value, $numString,$percent);
                if(intval($percent) > $max)
                {
                    $max = $percent;
                    $num = $key;
                    if(intval($percent) > 95)
                    {
                        break;
                    }
                }
            }
            $result[] = $num;
        }

        return $result;
    }
}
