<?php

namespace Scarpinocc\Periods\Once;

use Carbon\Carbon;
use Scarpinocc\Periods\EdgeInterface;

/**
 * TimestampEdge represents a specific point in time used as a boundary
 * for once-only periods.
 * 
 * This edge type is used in OncePeriod to define precise start and end
 * timestamps for one-time events or any other time-based restrictions 
 * that occur at specific dates and times.
 * 
 * The edge uses Carbon instances for precise timestamp comparisons
 * including timezone and millisecond accuracy.
 */
class TimestampEdge implements EdgeInterface
{
    /**
     * The specific timestamp that defines this edge
     * 
     * @var Carbon The Carbon instance representing the exact moment in time
     */
    public $timestamp;

    /**
     * Check if this edge's timestamp is before the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge's timestamp is strictly before the given time
     */
    public function before(Carbon $t)
    {
        return $this->timestamp->lt($t);
    }

    /**
     * Check if this edge's timestamp is after the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge's timestamp is strictly after the given time
     */
    public function after(Carbon $t)
    {
        return $this->timestamp->gt($t);
    }

    /**
     * Check if this edge's timestamp equals the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge's timestamp is exactly equal to the given time
     */
    public function equals(Carbon $t)
    {
        return $this->timestamp->eq($t);
    }

    /**
     * Check if this edge's timestamp is before or equal to the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge's timestamp is before or equal to the given time
     */
    public function beforeOrEquals(Carbon $t)
    {
        return $this->timestamp->lte($t);
    }

    /**
     * Check if this edge's timestamp is after or equal to the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge's timestamp is after or equal to the given time
     */
    public function afterOrEquals(Carbon $t)
    {
        return $this->timestamp->gte($t);
    }
}