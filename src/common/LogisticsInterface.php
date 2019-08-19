<?php

namespace sockball\logistics\common;

interface LogisticsInterface
{
    public function getLatestTrace(string  $waybillNo, bool $force = false);
    public function getFullTraces(string $waybillNo, bool $force = false);
    public function getOriginTraces(string $waybillNo, bool $force = false);
}
