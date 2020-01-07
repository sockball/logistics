<?php

namespace sockball\logistics;

use Exception;
use sockball\logistics\lib\Response;
use sockball\logistics\base\STO\STOLogistics;
use sockball\logistics\base\YTO\YTOLogistics;
use sockball\logistics\base\ZTO\ZTOLogistics;
use sockball\logistics\base\BEST\BESTLogistics;
use sockball\logistics\base\DANN\DANNLogistics;
use sockball\logistics\base\CHPO\CHPOLogistics;
use sockball\logistics\base\XVII\XVIILogistics;

class Logistics
{
    public const TYPE_BEST = BESTLogistics::CODE;
    public const TYPE_CHPO = CHPOLogistics::CODE;
    public const TYPE_DANN = DANNLogistics::CODE;
    public const TYPE_STO  = STOLogistics::CODE;
    public const TYPE_YTO  = YTOLogistics::CODE;
    public const TYPE_ZTO  = ZTOLogistics::CODE;
    public const TYPE_XVII = XVIILogistics::CODE;

    protected static $_logisticsInstances = [];
    private static $instance = null;
    private static $typeMappings = [
        self::TYPE_BEST => BESTLogistics::class,
        self::TYPE_CHPO => CHPOLogistics::class,
        self::TYPE_DANN => DANNLogistics::class,
        self::TYPE_STO  => STOLogistics::class,
        self::TYPE_YTO  => YTOLogistics::class,
        self::TYPE_ZTO  => ZTOLogistics::class,
        self::TYPE_XVII  => XVIILogistics::class,
    ];

    private function __construct(){}

    private function __clone(){}

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $type
     * @param string $waybillNo
     * @param array $options
     * @return Response
     * @throws Exception
     */
    public function query(string $type, string $waybillNo, array $options = [])
    {
        try {
            $instance = $this->getLogisticsInstance($type);
            $instance->beforeQuery();
        } catch (Exception $e) {
            return (new Response($waybillNo, $type))->setError($e->getMessage());
        }

        return $instance->query($waybillNo, $options);
    }

    protected function getLogisticsInstance($type)
    {
        $class = self::$typeMappings[$type] ?? null;
        if ($class === null)
        {
            throw new Exception('Unknown logistics type');
        }

        if (!isset(self::$_logisticsInstances[$type]))
        {
            self::$_logisticsInstances[$type] = new $class();
        }

        return self::$_logisticsInstances[$type];
    }
}
