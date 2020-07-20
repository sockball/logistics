<?php

namespace sockball\logistics\base;

abstract class BaseLogistics implements LogisticsInterface
{
    public const CODE = '';
    protected const REQUEST_URL = '';

    /**
     * 解析成统一格式的数据
     *
     * @param $raw
     * @return array
     */
    abstract protected function parseRaw($raw);

    /**
     * 请求成功后判断数据是否有效
     *
     * @param $result
     * @return bool
     */
    abstract protected function isValid($result);

    public function beforeQuery(){}
}
