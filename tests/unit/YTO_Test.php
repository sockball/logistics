<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class YTO_Test extends TestCase
{
    protected const VALID_NO = 'YT4234858984188';

    protected function setUp(): void
    {
        sleep(1);
    }

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_YTO, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_YTO, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }
}
