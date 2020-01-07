<?php

namespace tests\unit;

use sockball\logistics\Logistics;
use tests\TestCase;

class XVII_Test extends TestCase
{
    protected const VALID_NO = 'CI175732545JP';

    public function testSuccess(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));
    }

    public function testFailed(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, 'CI111111111JP');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));
    }

    public function testError(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, self::VALID_NO, ['python_cli' => 'wrong_cli']);
        $this->assertTrue($response->isError(), $this->getMessage($response));
    }
}
