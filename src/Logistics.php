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
use sockball\logistics\base\YUNDA\YUNDALogistics;

class Logistics
{
    public const TYPE_BEST  = BESTLogistics::CODE;
    public const TYPE_CHPO  = CHPOLogistics::CODE;
    public const TYPE_DANN  = DANNLogistics::CODE;
    public const TYPE_STO   = STOLogistics::CODE;
    public const TYPE_YTO   = YTOLogistics::CODE;
    public const TYPE_ZTO   = ZTOLogistics::CODE;
    public const TYPE_XVII  = XVIILogistics::CODE;
    public const TYPE_YUNDA = YUNDALogistics::CODE;

    protected static $_logisticsInstances = [];
    protected static $typeClassMappings = [
        // self::TYPE_BEST  => BESTLogistics::class,
        self::TYPE_CHPO  => CHPOLogistics::class,
        self::TYPE_DANN  => DANNLogistics::class,
        self::TYPE_STO   => STOLogistics::class,
        self::TYPE_YTO   => YTOLogistics::class,
        self::TYPE_ZTO   => ZTOLogistics::class,
        self::TYPE_XVII  => XVIILogistics::class,
        self::TYPE_YUNDA => YUNDALogistics::class,
    ];

    public function getSupportTypes()
    {
        return [
            // self::TYPE_BEST  => '百世快递',
            self::TYPE_CHPO  => '中国邮政',
            self::TYPE_DANN  => '丹鸟快递',
            self::TYPE_STO   => '申通快递',
            self::TYPE_YTO   => '圆通快递',
            self::TYPE_ZTO   => '中通快递',
            self::TYPE_XVII  => '17track',
            self::TYPE_YUNDA => '韵达快递',
        ];
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
        $class = self::$typeClassMappings[$type] ?? null;
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
