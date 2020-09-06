<?php

namespace tests\unit;

use sockball\logistics\base\Trace;
use sockball\logistics\lib\Response;
use sockball\logistics\Logistics;
use tests\TestCase;

class XVII_Test extends TestCase
{
    protected const VALID_NO = 'CI175732545JP';

    public function testSuccess(): Response
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, self::VALID_NO);
        $this->assertTrue($response->isSuccess(), $this->getMessage($response));

        return $response;
    }

    public function testFailed(): Response
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, 'CI111111111JP');
        $this->assertTrue($response->isFailed(), $this->getMessage($response));

        return $response;
    }

    public function testError(): void
    {
        $response = self::$logistics->query(Logistics::TYPE_XVII, self::VALID_NO, ['python_cli' => 'wrong_cli']);
        $this->assertTrue($response->isError(), $this->getMessage($response));
    }

    /**
     * @param Response $success
     * @param Response $failed
     * @depends testSuccess
     * @depends testFailed
     */
    public function testResponse(Response $success, Response $failed)
    {
        $this->assertNotNull($success->info);
        foreach ($success as $trace)
        {
            /** @var Trace $trace */
            $this->assertNotNull($trace->info);
            break;
        }

        foreach ($failed as $trace)
        {
            break;
        }

        $attribute = 'not_exist';
        $this->expectExceptionMessage("property {$attribute} is not exist");
        $success->$attribute;
    }
}
