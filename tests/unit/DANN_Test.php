<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class DANN_Test extends TestCase
{
    protected const VALID_NO = '611090452344701';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_DANN, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_DANN, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }
}
