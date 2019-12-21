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
 * @property string       $msg
 * @property string       $error
 * @property int          $statusCode
 *
 * @property int          $timestamp
 * @property string       $info
 * @property string       $state
 */
class Response extends BaseObject implements IteratorAggregate
{
    public const RESPONSE_SUCCESS = 200;
    public const RESPONSE_FAILED  = 400;
    public const RESPONSE_ERROR   = 500;

    public $type;
    public $waybillNo;

    protected $raw;
    protected $traces;
    protected $msg;
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

    public function __construct(string $waybillNo, string $type)
    {
        $this->setAttributes([
            'waybillNo' => $waybillNo,
            'type' => $type,
        ]);
    }

    public function setSuccess(array $traces)
    {
        $this->setAttributes([
            'traces' => $traces,
            'statusCode' => self::RESPONSE_SUCCESS,
        ]);

        return $this;
    }

    public function setFailed(string $msg)
    {
        $this->setAttributes([
            'msg' => $msg,
            'statusCode' => self::RESPONSE_FAILED,
        ]);

        return $this;
    }

    public function setError(string $error)
    {
        $this->setAttributes([
            'error' => $error,
            'statusCode' => self::RESPONSE_ERROR,
        ]);

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

    public function getMsg()
    {
        return $this->msg;
    }

    public function getError()
    {
        return $this->error;
    }

    public function isSuccess()
    {
        return $this->statusCode === self::RESPONSE_SUCCESS;
    }

    public function isError()
    {
        return $this->statusCode === self::RESPONSE_ERROR;
    }
}
