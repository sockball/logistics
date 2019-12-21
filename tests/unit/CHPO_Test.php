<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class CHPO_Test extends TestCase
{
    protected const VALID_NO = '9860401996391';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_CHPO, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_CHPO, 'error waybill_no');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }

    public function testError(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_CHPO, 'error waybill_no', ['python_cli' => 'wrong_cli']);
        $this->assertTrue($response->isError(), $this->getMessage($response));
    }
}
