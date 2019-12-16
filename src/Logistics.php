<?php

namespace sockball\logistics;

use sockball\logistics\base\STO\STOLogistics;
use sockball\logistics\base\YTO\YTOLogistics;
use sockball\logistics\base\ZTO\ZTOLogistics;
use sockball\logistics\base\BEST\BESTLogistics;
use sockball\logistics\base\DANN\DANNLogistics;
use sockball\logistics\base\CHPO\CHPOLogistics;

class Logistics
{
    public const TYPE_BEST = 'baishi';
    public const TYPE_DANN = 'danniao';
    public const TYPE_CHPO = 'china post';
    public const TYPE_STO = 'sto';
    public const TYPE_YTO = 'yto';
    public const TYPE_ZTO = 'zto';
    public const RESPONSE_SUCCESS = 0;
    public const RESPONSE_FAILED = -1;

    protected static $_logisticsInstances = [];
    private static $instance = null;
    private static $typeMappings = [
        self::TYPE_BEST => BESTLogistics::class,
        self::TYPE_CHPO => CHPOLogistics::class,
        self::TYPE_DANN => DANNLogistics::class,
        self::TYPE_STO  => STOLogistics::class,
        self::TYPE_YTO  => YTOLogistics::class,
        self::TYPE_ZTO  => ZTOLogistics::class,
    ];

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getLatestTrace(string $type, string $waybillNo, bool $force = false)
    {
        return $this->getLogisticsInstance($type)->getLatestTrace($waybillNo, $force);
    }

    public function getFullTraces(string $type, string $waybillNo, bool $force = false)
    {
        return $this->getLogisticsInstance($type)->getFullTraces($waybillNo, $force);
    }

    public function getOriginTraces(string $type, string $waybillNo, bool $force = false)
    {
        return $this->getLogisticsInstance($type)->getOriginTraces($waybillNo, $force);
    }

    protected function getLogisticsInstance($type)
    {
        $class = self::$typeMappings[$type] ?? null;
        if ($class === null)
        {
            throw new \Exception('Unknown logistics type');
        }

        if (!isset(self::$_logisticsInstances[$type]))
        {
            self::$_logisticsInstances[$type] = new $class();
        }

        return self::$_logisticsInstances[$type];
    }
}
