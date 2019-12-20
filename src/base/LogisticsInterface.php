<?php

namespace sockball\logistics\base;

interface LogisticsInterface
{
    public function query(string  $waybillNo, array $options = []);
}
