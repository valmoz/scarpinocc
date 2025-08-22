<?php

namespace Scarpinocc\Periods\Always;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

/**
 * AlwaysPeriod represents a period that always contains any time.
 * 
 * All methods will always return true, making this period completely inclusive.
 */
class AlwaysPeriod implements PeriodInterface
{
    /**
     * Check if a specific time is contained in this period
     * 
     * AlwaysPeriod always returns true regardless of the input time,
     * as this period type is designed to always contain every moment.
     * 
     * @param Carbon $t The time to check (accepted but not evaluated)
     * @return bool Always returns true
     */
    public function contains(Carbon $t)
    {
        return true;
    }

    /**
     * Check if current time is contained in this period
     * 
     * AlwaysPeriod always returns true as it always contains any time,
     * including the current moment.
     * 
     * @return bool Always returns true
     */
    public function containsNow()
    {
        return true;
    }
}