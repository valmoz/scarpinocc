<?php

namespace Scarpinocc\Periods\Weekly;

use Carbon\Carbon;
use Scarpinocc\Periods\EdgeInterface;

class DayTimeEdge implements EdgeInterface
{
    /**
     * @var $day int
     */
    public $day;

    /**
     * @var $hour string
     */
    public $hour;

    public function before(Carbon $t)
    {
        if ($this->day < $t->isoWeekday()) {
            return true;
        }
        if ($this->day > $t->isoWeekday()) {
          return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->lt($t);
    }

    public function after(Carbon $t)
    {
        if ($this->day > $t->isoWeekday()) {
            return true;
        }
        if ($this->day < $t->isoWeekday()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->gt($t);
    }

    public function equals(Carbon $t)
    {
        if ($this->day !== $t->isoWeekday()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->eq($t);
    }

    public function beforeOrEquals(Carbon $t)
    {
        if ($this->day < $t->isoWeekday()) {
            return true;
        }
        if ($this->day > $t->isoWeekday()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->lte($t);
    }

    public function afterOrEquals(Carbon $t)
    {
        if ($this->day > $t->isoWeekday()) {
            return true;
        }
        if ($this->day < $t->isoWeekday()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->gte($t);
    }

    private function getEdgeTime(Carbon $t)
    {
        if ($this->day !=  $t->isoWeekday()) {
            throw new \Exception("different day from edge");
        }
        $str = explode(':', $this->hour);
        $hour = (int)$str[0];
        $minute = (int)$str[1];
        return Carbon::create($t->year, $t->month, $t->day, $hour, $minute, 0, $t->tz);
    }
}