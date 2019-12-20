<?php

namespace sockball\logistics\lib;

class BaseObject
{
    /**
     * @param array $options
     * @param bool $checkEmpty 检测值是否为空 true且为空时则忽略（注意0也为空...
     */
    protected function setAttributes(array $options, bool $checkEmpty = false)
    {
        foreach ($options as $attribute => $value)
        {
            if (!($checkEmpty && empty($value)))
            {
                $this->$attribute = $value;
            }
        }
    }
}
