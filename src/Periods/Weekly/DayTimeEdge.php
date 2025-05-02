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
     * @var $time string
     */
    public $time;

    public function before(Carbon $t)
    {
        if ($this->day < $t->weekDay()) {
            return true;
        }
        if ($this->day > $t->weekDay()) {
          return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->lt($this->time);
    }

    public function after(Carbon $t)
    {
        if ($this->day > $t->weekDay()) {
            return true;
        }
        if ($this->day < $t->weekDay()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->gt($this->time);
    }

    public function equals(Carbon $t)
    {
        if ($this->day !== $t->weekDay()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->eq($this->time);
    }

    public function beforeOrEquals(Carbon $t)
    {
        if ($this->day < $t->weekDay()) {
            return true;
        }
        if ($this->day > $t->weekDay()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->lte($this->time);
    }

    public function afterOrEquals(Carbon $t)
    {
        if ($this->day > $t->weekDay()) {
            return true;
        }
        if ($this->day < $t->weekDay()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->gte($this->time);
    }

    private function getEdgeTime($t)
    {
        if ($this->day !=  $t->weekDay()) {
            throw new \Exception("different day from edge");
        }
        $str = explode(':', $this->time);
        $hour = (int)$str[0];
        $minute = (int)$str[1];
        return Carbon::create($t->year, $t->month, $t->day, $hour, $minute, 0);
    }
}