<?php

namespace sockball\logistics\base;

use sockball\logistics\lib\BaseObject;

/**
 * Class Trace
 *
 * @property int         $timestamp
 * @property string      $info
 * @property string|null $state
 */
class Trace extends BaseObject
{
    public $timestamp;
    public $info;
    public $state;

    public function __construct(int $timestamp, string $info, $state)
    {
        $this->setAttributes([
            'timestamp' => $timestamp,
            'info' => $info,
            'state' => $state,
        ]);
    }
}
