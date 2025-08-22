<?php

namespace Scarpinocc\Periods\Daily;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

/**
 * DailyPeriod represents a recurring daily time period defined by
 * two TimeEdge boundaries that repeat every day.
 * 
 * This period type supports both internal periods (e.g., 09:00 to 17:00)
 * and external periods that span across midnight (e.g., 22:00 to 06:00).
 * 
 * The period can also handle same-time periods where from and to are identical,
 * representing a single moment that occurs daily.
 */
class DailyPeriod implements PeriodInterface
{
    /**
     * The starting edge of the daily period
     * 
     * @var TimeEdge The edge defining when the period begins each day
     */
    public $from;

    /**
     * The ending edge of the daily period
     * 
     * @var TimeEdge The edge defining when the period ends each day
     */
    public $to;

    /**
     * Check if a specific time is contained within this daily period
     * 
     * The method handles three scenarios:
     * 1. Internal periods: from < to (normal day-internal periods)
     * 2. External periods: from > to (periods spanning midnight)
     * 3. Same-time periods: from == to (single moment periods)
     * 
     * @param Carbon $t The time to check
     * @return bool True if the time falls within the daily period
     */
    public function contains(Carbon $t)
    {
        if ($this->from->hour < $this->to->hour) {
            // Internal period: from < to (e.g., 09:00 to 17:00)
            return $this->from->beforeOrEquals($t) && $this->to->afterOrEquals($t);
        } elseif ($this->from->hour > $this->to->hour) {
            // External period: from > to (e.g., 22:00 to 06:00)
            return $this->to->afterOrEquals($t) || $this->from->beforeOrEquals($t);
        } else {
            // Same time: from == to (single moment)
            return $this->from->equals($t);
        }
    }

    /**
     * Check if the current time is contained within this daily period
     * 
     * This is a convenience method that calls contains() with the current time.
     * 
     * @return bool True if the current time falls within the daily period
     */
    public function containsNow()
    {
        return $this->contains(Carbon::now());
    }
}