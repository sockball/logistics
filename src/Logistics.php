<?php

namespace sockball\logistics;

use stdClass;
use sockball\logistics\base\STO\STOLogistics;
use sockball\logistics\base\YTO\YTOLogistics;
use sockball\logistics\base\ZTO\ZTOLogistics;

class Logistics
{
    public const TYPE_STO = 'sto';
    public const TYPE_YTO = 'yto';
    public const TYPE_ZTO = 'zto';

    protected static $_logisticsInstances = [];

    public static function getLatestTrace(string $type, string $waybillNo, bool $force = false)
    {
        $instance = self::getInstance($type);
        return $instance->getLatestTrace($waybillNo, $force);
    }

    public static function getFullTraces(string $type, string $waybillNo, bool $force = false)
    {
        $instance = self::getInstance($type);
        return $instance->getFullTraces($waybillNo, $force);
    }

    public static function getOriginTraces(string $type, string $waybillNo, bool $force = false)
    {
        $instance = self::getInstance($type);
        return $instance->getOriginTraces($waybillNo, $force);
    }

    protected static function getInstance($type)
    {
        switch ($type)
        {
            case self::TYPE_STO:
            default:
                if (!isset(self::$_logisticsInstances[$type]))
                {
                    self::$_logisticsInstances[$type] = new STOLogistics();
                }
                break;

            case self::TYPE_YTO:
                if (!isset(self::$_logisticsInstances[$type]))
                {
                    self::$_logisticsInstances[$type] = new YTOLogistics();
                }
                break;

            case self::TYPE_ZTO:
                if (!isset(self::$_logisticsInstances[$type]))
                {
                    self::$_logisticsInstances[$type] = new ZTOLogistics();
                }
                break;
        }

        return self::$_logisticsInstances[$type];
    }
}
