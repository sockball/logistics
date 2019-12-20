<?php

namespace sockball\logistics\lib;

use Exception;
use IteratorAggregate;

/**
 * Class Response
 *
 * @property string       $type
 * @property string       $waybillNo
 * @property array|string $raw
 * @property array        $traces
 * @property string       $error
 * @property int          $statusCode
 *
 * @property int          $timestamp
 * @property string       $info
 * @property string       $state
 */
class Response extends BaseObject implements IteratorAggregate
{
    public const RESPONSE_SUCCESS = 1;
    public const RESPONSE_FAILED  = -1;

    public $type;
    public $waybillNo;

    protected $raw;
    protected $traces;
    protected $error;
    protected $statusCode;

    protected static $traceAttribute = ['timestamp', 'info', 'state'];

    public function __get($attribute)
    {
        if (in_array($attribute, self::$traceAttribute))
        {
            return $this->traces[0]->$attribute ?? null;
        }

        throw new Exception("property {$attribute} is not exist");
    }

    public function __construct($raw, int $statusCode = self::RESPONSE_SUCCESS, ?string $error = null)
    {
        $this->setAttributes([
            'raw' => $raw,
            'statusCode' => $statusCode,
            'error' => $error,
        ], true);
    }

    public function setSuccess(string $waybillNo, string $type, array $traces)
    {
        $this->setAttributes([
            'traces' => $traces,
            'waybillNo' => $waybillNo,
            'type' => $type,
            'statusCode' => self::RESPONSE_SUCCESS,
        ]);

        return $this;
    }

    public function setFailed(string $waybillNo, string $type, ?string $error = null)
    {
        $this->setAttributes([
            'error' => $error,
            'waybillNo' => $waybillNo,
            'type' => $type,
            'statusCode' => self::RESPONSE_FAILED,
        ], true);

        return $this;
    }

    public function getIterator()
    {
        return $this->iterator();
    }

    private function iterator()
    {
        if (!empty($this->traces))
        {
            foreach ($this->traces as $trace)
            {
                yield $trace;
            }
        }
    }

    public function getLatest()
    {
        return $this->traces[0] ?? null;
    }

    public function getAll()
    {
        return $this->traces;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getError()
    {
        return $this->error;
    }

    public function isSuccess()
    {
        return $this->statusCode === self::RESPONSE_SUCCESS;
    }
}
