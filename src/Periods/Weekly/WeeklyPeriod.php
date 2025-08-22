<?php

namespace Scarpinocc\Periods\Weekly;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

/**
 * WeeklyPeriod represents a recurring weekly time period defined by
 * two DayTimeEdge boundaries that repeat every week.
 * 
 * This period type supports both internal periods (e.g., Tuesday 09:00 to Friday 17:00)
 * and external periods that span across the week boundary (e.g., Friday 22:00 to Monday 08:00).
 * 
 * The period can also handle same-day periods (e.g., Wednesday 09:00 to Wednesday 17:00)
 * and even single-moment periods where from and to are identical.
 */
class WeeklyPeriod implements PeriodInterface
{
    /**
     * The starting edge of the weekly period
     * 
     * @var DayTimeEdge The edge defining when the period begins each week
     */
    public $from;

    /**
     * The ending edge of the weekly period
     * 
     * @var DayTimeEdge The edge defining when the period ends each week
     */
    public $to;

    /**
     * Check if a specific time is contained within this weekly period
     * 
     * The method handles three scenarios:
     * 1. Internal periods: from < to (normal week-internal periods)
     * 2. External periods: from > to (periods spanning week boundaries)
     * 3. Same-edge periods: from == to (single moment periods)
     * 
     * @param Carbon $t The time to check
     * @return bool True if the time falls within the weekly period
     */
    public function contains(Carbon $t)
    {
        if ($this->from->day < $this->to->day || ($this->from->day == $this->to->day && $this->from->hour < $this->to->hour)) {
            return $this->from->beforeOrEquals($t) && $this->to->afterOrEquals($t);
        }
        if ($this->from->day > $this->to->day || ($this->from->day == $this->to->day && $this->from->hour > $this->to->hour)) {
            return $this->to->afterOrEquals($t) || $this->from->beforeOrEquals($t);
        }
        // in this case from and to are the same
        return $this->from->equals($t);
    }

    /**
     * Check if the current time is contained within this weekly period
     * 
     * This is a convenience method that calls contains() with the current time.
     * 
     * @return bool True if the current time falls within the weekly period
     */
    public function containsNow()
    {
        return $this->contains(Carbon::now());
    }
}