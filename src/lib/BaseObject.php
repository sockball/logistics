<?php

namespace sockball\logistics\lib;

class BaseObject
{
    /**
     * @param array $options
     * @param bool $checkEmpty When this is true and value is empty, this attribute will ignore (note that 0 is empty too)
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
