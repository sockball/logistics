<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class ZTO_Test extends TestCase
{
    protected const VALID_NO = '75166031906321';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_ZTO, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_ZTO, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }
}
