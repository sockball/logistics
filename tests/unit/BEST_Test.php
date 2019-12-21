<?php

namespace tests\unit;

use sockball\logistics\base\Trace;
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

    public function testUseUnknownType()
    {
        $this->expectExceptionMessage('Unknown logistics type');
        self::$logistics->query('not exist type', 'waybill');
    }
}
