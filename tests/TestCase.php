<?php

namespace tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use sockball\logistics\lib\Response;
use sockball\logistics\Logistics;

class TestCase extends BaseTestCase
{
    protected const VALID_NO = '';

    /** @var Logistics */
    protected static $logistics;

    public static function setUpBeforeClass(): void
    {
        if (self::$logistics === null)
        {
            self::$logistics = new Logistics();
        }
    }

    protected function getMessage(Response $response)
    {
        return ($response->isFailed() ? $response->getMsg() : $response->getError()) ?? '';
    }
}
