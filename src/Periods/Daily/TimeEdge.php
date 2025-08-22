<?php

namespace Scarpinocc\Periods\Daily;

use Carbon\Carbon;
use Scarpinocc\Periods\EdgeInterface;

/**
 * TimeEdge represents a daily recurring time boundary defined by
 * a specific time within the day (hour and minute).
 * 
 * This edge type is used for daily periods that repeat every day
 * at the same time, such as "every day at 09:30" or "daily at 17:00".
 * 
 * The time is stored as a string in "HH:MM" format and is applied
 * to any given date to create precise timestamp comparisons.
 */
class TimeEdge implements EdgeInterface
{
    /**
     * The time within the day in "HH:MM" format
     * 
     * @var string Time string in 24-hour format (e.g., "09:30", "17:00", "23:59")
     */
    public $hour;

    /**
     * Check if this edge occurs before the given time on the same day
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs before the given time
     */
    public function before(Carbon $t)
    {
        try {
            $edgeTimestamp = $this->getEdgeTimestamp($t);
            return $edgeTimestamp->lt($t);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if this edge occurs after the given time on the same day
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs after the given time
     */
    public function after(Carbon $t)
    {
        try {
            $edgeTimestamp = $this->getEdgeTimestamp($t);
            return $edgeTimestamp->gt($t);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if this edge occurs at exactly the same time as the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs at exactly the same time
     */
    public function equals(Carbon $t)
    {
        try {
            $edgeTimestamp = $this->getEdgeTimestamp($t);
            return $edgeTimestamp->eq($t);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if this edge occurs before or at the same time as the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs before or at the same time
     */
    public function beforeOrEquals(Carbon $t)
    {
        return $this->before($t) || $this->equals($t);
    }

    /**
     * Check if this edge occurs after or at the same time as the given time
     * 
     * @param Carbon $t The time to compare against
     * @return bool True if this edge occurs after or at the same time
     */
    public function afterOrEquals(Carbon $t)
    {
        return $this->after($t) || $this->equals($t);
    }

    /**
     * Create a Carbon instance representing this edge's time on the same date as the given time
     * 
     * This method takes the hour property and applies it to the same date as the baseTime,
     * preserving the timezone and creating a precise timestamp for comparison.
     * 
     * @param Carbon $t The reference time providing date context and timezone
     * @return Carbon A Carbon instance representing this edge's exact time
     * @throws \InvalidArgumentException If the hour format is invalid
     */
    private function getEdgeTimestamp(Carbon $t)
    {
        $tokens = explode(':', $this->hour);
        if (count($tokens) !== 2) {
            throw new \InvalidArgumentException("Invalid hour format: {$this->hour}");
        }

        $hour = (int) $tokens[0];
        $min = (int) $tokens[1];

        if ($hour < 0 || $hour > 23) {
            throw new \InvalidArgumentException("Invalid hour value: {$tokens[0]}");
        }

        if ($min < 0 || $min > 59) {
            throw new \InvalidArgumentException("Invalid minute value: {$tokens[1]}");
        }

        return Carbon::create($t->year, $t->month, $t->day, $hour, $min, 0, $t->tz);
    }
}