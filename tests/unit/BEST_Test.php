<?php

namespace tests\unit;

use sockball\logistics\lib\Response;
use sockball\logistics\Logistics;
use tests\TestCase;

class BEST_Test extends TestCase
{
    protected const VALID_NO = '70577935260961';

    public function testSuccess(): Response
    {
        $response = self::$logistics->query(Logistics::TYPE_BEST, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));

        return $response;
    }

    public function testFailed(): Response
    {
        $response = self::$logistics->query(Logistics::TYPE_BEST, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));

        return $response;
    }

    public function testUseUnknownType()
    {
        $response = self::$logistics->query('not exist type', 'waybill');
        $this->assertEquals('Unknown logistics type', $response->getError());
    }
}
