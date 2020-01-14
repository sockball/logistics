<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class YUNDA_Test extends TestCase
{
    protected const VALID_NO = '4302078286228';
    protected const NON_EXIST_NO = '4402078286228';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_YUNDA, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_YUNDA, self::NON_EXIST_NO);
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }

    public function testError(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_YUNDA, self::VALID_NO, ['python_cli' => 'wrong_cli']);
        $this->assertTrue($response->isError(), $this->getMessage($response));
    }
}
