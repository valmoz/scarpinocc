<?php

namespace Scarpinocc\Periods\Once;

use Carbon\Carbon;
use Scarpinocc\Periods\EdgeInterface;

class TimestampEdge implements EdgeInterface
{
    /**
     * @var $timestamp Carbon
     */
    public $timestamp;

    public function before(Carbon $t)
    {
        return $this->timestamp->lt($t);
    }

    public function after(Carbon $t)
    {
        return $this->timestamp->gt($t);
    }

    public function equals(Carbon $t)
    {
        return $this->timestamp->eq($t);
    }

    public function beforeOrEquals(Carbon $t)
    {
        return $this->timestamp->lte($t);
    }

    public function afterOrEquals(Carbon $t)
    {
        return $this->timestamp->gte($t);
    }

}