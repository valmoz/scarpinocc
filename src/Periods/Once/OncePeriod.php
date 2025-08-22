<?php

namespace Scarpinocc\Periods\Once;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

/**
 * OncePeriod represents a time period that occurs only once,
 * defined by a start and end timestamp.
 * 
 * This period type is used for one-time events or any other 
 * time-based restrictions that have a specific start
 * and end date/time.
 */
class OncePeriod implements PeriodInterface
{
    /**
     * The start edge of the period
     * 
     * @var TimestampEdge The starting timestamp edge
     */
    public $from;

    /**
     * The end edge of the period
     * 
     * @var TimestampEdge The ending timestamp edge
     */
    public $to;

    /**
     * Check if a specific time is contained within this period
     * 
     * A time is considered contained if it falls between (inclusive)
     * the from and to timestamps.
     * 
     * @param Carbon $t The time to check
     * @return bool True if the time is within the period, false otherwise
     */
    public function contains(Carbon $t)
    {
        return $this->from->beforeOrEquals($t) && $this->to->afterOrEquals($t);
    }

    /**
     * Check if the current time is contained within this period
     * 
     * This is a convenience method that calls contains() with Carbon::now().
     * 
     * @return bool True if the current time is within the period, false otherwise
     */
    public function containsNow()
    {
        return $this->contains(Carbon::now());
    }
}