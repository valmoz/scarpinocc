<?php

namespace Scarpinocc\Periods\Weekly;

use Carbon\Carbon;
use Scarpinocc\Periods\EdgeInterface;

/**
 * DayTimeEdge represents a recurring weekly time boundary defined by
 * a day of the week and a specific time within that day.
 * 
 * This edge type is used in WeeklyPeriod to define recurring boundaries
 * that repeat every week. For example, "every Tuesday at 09:30" or
 * "every Friday at 17:00".
 * 
 * The day is represented using ISO weekday format (1=Monday, 2=Tuesday, ..., 7=Sunday)
 * and the time is stored as a string in "HH:MM" format.
 */
class DayTimeEdge implements EdgeInterface
{
    /**
     * The day of the week using ISO weekday format
     * 
     * @var int Day number (1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday, 7=Sunday)
     */
    public $day;

    /**
     * The time within the day in "HH:MM" format
     * 
     * @var string Time string in 24-hour format (e.g., "09:30", "17:00", "23:59")
     */
    public $hour;

    /**
     * Check if this edge occurs before the given time in the weekly cycle
     * 
     * Compares first by day of week, then by time within the day if the days match.
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs before the given time
     */
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

    /**
     * Check if this edge occurs after the given time in the weekly cycle
     * 
     * Compares first by day of week, then by time within the day if the days match.
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs after the given time
     */
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

    /**
     * Check if this edge occurs at exactly the same time as the given time
     * 
     * Both the day of week and time within the day must match exactly.
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs at exactly the same time
     */
    public function equals(Carbon $t)
    {
        if ($this->day !== $t->isoWeekday()) {
            return false;
        }

        $edgeTime = $this->getEdgeTime($t);
        return $edgeTime->eq($t);
    }

    /**
     * Check if this edge occurs before or at the same time as the given time
     * 
     * This method is commonly used for "from" edges in weekly period checks,
     * where the boundary time should be included in the period.
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs before or at the same time
     */
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

    /**
     * Check if this edge occurs after or at the same time as the given time
     * 
     * This method is commonly used for "to" edges in weekly period checks,
     * where the boundary time should be included in the period.
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs after or at the same time
     */
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

    /**
     * Create a Carbon instance representing this edge's time on the same date as the given time
     * 
     * This helper method constructs a precise timestamp by combining the edge's
     * day and hour properties with the date context from the given Carbon instance.
     * 
     * @param Carbon $t The reference time providing date context and timezone
     * @return Carbon A Carbon instance representing this edge's exact time
     * @throws \Exception If the edge day doesn't match the given time's day of week
     */
    private function getEdgeTime(Carbon $t)
    {
        if ($this->day !=  $t->isoWeekday()) {
            throw new \Exception("different day from edge");
        }
        $tokens = explode(':', $this->hour);
        if (count($tokens) !== 2) {
            throw new \InvalidArgumentException("Invalid hour format: {$this->hour}");
        }

        $hour = (int)$tokens[0];
        $min = (int)$tokens[1];

        if ($hour < 0 || $hour > 23) {
            throw new \InvalidArgumentException("Invalid hour value: {$tokens[0]}");
        }

        if ($min < 0 || $min > 59) {
            throw new \InvalidArgumentException("Invalid minute value: {$tokens[1]}");
        }

        return Carbon::create($t->year, $t->month, $t->day, $hour, $min, 0, $t->tz);
    }
}