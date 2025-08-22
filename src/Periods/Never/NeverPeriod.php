<?php

namespace Scarpinocc\Periods\Never;

use Carbon\Carbon;
use Scarpinocc\Periods\PeriodInterface;

/**
 * NeverPeriod represents a period that never contains any time.
 * 
 * All methods will always return false, making this period completely inactive.
 */
class NeverPeriod implements PeriodInterface
{
    /**
     * Check if a specific time is contained in this period
     * 
     * NeverPeriod always returns false regardless of the input time,
     * as this period type is designed to never contain any moment.
     * 
     * @param Carbon $t The time to check (ignored in this implementation)
     * @return bool Always returns false
     */
    public function contains(Carbon $t)
    {
        return false;
    }

    /**
     * Check if current time is contained in this period
     * 
     * NeverPeriod always returns false as it never contains any time,
     * including the current moment.
     * 
     * @return bool Always returns false
     */
    public function containsNow()
    {
        return false;
    }
}