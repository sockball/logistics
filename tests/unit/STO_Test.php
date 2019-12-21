<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class STO_Test extends TestCase
{
    protected const VALID_NO = '773017017415724';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_STO, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_STO, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }
}
