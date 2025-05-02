<?php

namespace Scarpinocc\Periods\Weekly;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

class WeeklyPeriod implements PeriodInterface
{
    /**
     * @var $from DayTimeEdge
     */
    public $from;

    /**
     * @var $to DayTimeEdge
     */
    public $to;

    public function contains(Carbon $t)
    {
        if ($this->from->day < $this->to->day || ($this->from->day == $this->to->day && $this->from->time < $this->to->time)) {
            return $this->from->beforeOrEquals($t) && $this->to->afterOrEquals($t);
        }
        if ($this->from->day > $this->to->day || ($this->from->day == $this->to->day && $this->from->time > $this->to->time)) {
            return $this->to->afterOrEquals($t) || $this->from->beforeOrEquals($t);
        }
        // in this case from and to are the same
        return $this->from->equals($t);
    }

    public function containsNow()
    {
        return $this->contains(Carbon::now());
    }
}