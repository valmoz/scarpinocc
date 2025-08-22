<?php

namespace Scarpinocc;

use Carbon\Carbon;
use Scarpinocc\Periods\Once\TimestampEdge;
use Scarpinocc\Periods\PeriodInterface;
use Scarpinocc\Periods\Weekly\DayTimeEdge;
use Scarpinocc\Periods\Weekly\WeeklyPeriod;
use Scarpinocc\Periods\Once\OncePeriod;
use Scarpinocc\Periods\Always\AlwaysPeriod;
use Scarpinocc\Periods\Never\NeverPeriod;
use Scarpinocc\Periods\Daily\DailyPeriod;
use Scarpinocc\Periods\Daily\TimeEdge;

class Scarpinocc 
{
    /**
     * @var PeriodInterface[]
     */
    public $periods = [];

    /**
     * Create Scarpinocc from JSON data
     * 
     * @param array $data Decoded JSON data
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function fromArray(array $data)
    {
        $scarpinocc = new self();
        
        if (!isset($data['periods']) || !is_array($data['periods'])) {
            throw new \InvalidArgumentException('periods field is required and must be an array');
        }

        $periods = [];
        foreach ($data['periods'] as $periodData) {
            if (!isset($periodData['type'])) {
                throw new \InvalidArgumentException('period type is required');
            }

            switch ($periodData['type']) {
                case 'weekly':
                    $period = new WeeklyPeriod();
                    if (isset($periodData['from'])) {
                        $from = new DayTimeEdge();
                        $from->day = self::mapDayToNumber($periodData['from']['day'] ?? 0);
                        $from->hour = $periodData['from']['hour'] ?? '00:00';
                        $period->from = $from;
                    }
                    if (isset($periodData['to'])) {
                        $to = new DayTimeEdge();
                        $to->day = self::mapDayToNumber($periodData['to']['day'] ?? 0);
                        $to->hour = $periodData['to']['hour'] ?? '00:00';
                        $period->to = $to;
                    }
                    $periods[] = $period;
                    break;
                    
                case 'daily':
                    $period = new DailyPeriod();
                    if (isset($periodData['from'])) {
                        $from = new TimeEdge();
                        $from->hour = $periodData['from']['hour'] ?? '00:00';
                        $period->from = $from;
                    }
                    if (isset($periodData['to'])) {
                        $to = new TimeEdge();
                        $to->hour = $periodData['to']['hour'] ?? '23:59';
                        $period->to = $to;
                    }
                    $periods[] = $period;
                    break;
                    
                case 'once':
                    $period = new OncePeriod();
                    if (isset($periodData['from'])) {
                        $from = new TimestampEdge();
                        $from->timestamp = Carbon::parse($periodData['from']['timestamp']) ?? Carbon::now();
                        $period->from = $from;
                    }
                    if (isset($periodData['to'])) {
                        $to = new TimestampEdge();
                        $to->timestamp = Carbon::parse($periodData['to']['timestamp']) ?? Carbon::now();
                        $period->to = $to;
                    }
                    $periods[] = $period;
                    break;
                    
                case 'always':
                    $period = new AlwaysPeriod();
                    $periods[] = $period;
                    break;
                    
                case 'never':
                    $period = new NeverPeriod();
                    $periods[] = $period;
                    break;
                    
                default:
                    throw new \InvalidArgumentException("Unknown period type: {$periodData['type']}");
            }
        }

        $scarpinocc->periods = $periods;
        return $scarpinocc;
    }

    /**
     * Map day name to numeric value
     * 
     * @param string|int $day Day name or number
     * @return int Day number (1=Monday, 2=Tuesday, ..., 7=Sunday) - ISO weekday format
     */
    private static function mapDayToNumber($day)
    {
        if (is_numeric($day)) {
            return (int) $day;
        }
        
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7,
        ];
        
        $dayLower = strtolower($day);
        if (isset($dayMap[$dayLower])) {
            return $dayMap[$dayLower];
        }
        
        throw new \InvalidArgumentException("Invalid day name: {$day}");
    }

    /**
     * Create Scarpinocc from JSON string
     * 
     * @param string $json JSON string
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function fromJson(string $json)
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        return self::fromArray($data);
    }

    /**
     * Check if a specific time is contained in any period
     * 
     * @param Carbon $t The time to check
     * @return bool
     */
    public function contains(Carbon $t)
    {
        if (!$this->periods) {
            return false;
        }

        foreach ($this->periods as $period) {
            if ($period->contains($t)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if current time is contained in any period
     * 
     * @return bool
     */
    public function containsNow()
    {
        if (!$this->periods) {
            return false;
        }
        
        foreach ($this->periods as $period) {
            if ($period->containsNow()) {
                return true;
            }
        }
        return false;
    }
}