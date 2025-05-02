<?php

namespace Scarpinocc\Periods\Once;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

class OncePeriod implements PeriodInterface
{

    /**
     * @var $from TimestampEdge
     */
    public $from;

    /**
     * @var $to TimestampEdge
     */
    public $to;

    public function contains(Carbon $t)
    {
        return $this->from->beforeOrEquals($t) && $this->to->afterOrEquals($t);
    }

    public function containsNow()
    {
        return $this->contains(Carbon::now());
    }
}