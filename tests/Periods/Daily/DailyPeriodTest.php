<?php

namespace Scarpinocc\Test\Periods\Daily;

use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Daily\DailyPeriod;
use Scarpinocc\Periods\Daily\TimeEdge;
use Scarpinocc\Periods\PeriodLabel;
use Carbon\Carbon;

class DailyPeriodTest extends TestCase
{
    protected static $initialized = false;

    protected function setUp(): void
    {
       parent::setUp();
        if (!self::$initialized) {
            date_default_timezone_set('Europe/Rome');
            self::$initialized = true;
        }
    }

    public function testDailyPeriodInternalContains(): void
    {
        // Internal case test: 09:00 <= x <= 17:00
        $from = new TimeEdge();
        $from->hour = '09:00';

        $to = new TimeEdge();
        $to->hour = '17:00';

        $period = new DailyPeriod();
        $period->from = $from;
        $period->to = $to;

        // Test time before the period
        $beforeTime = Carbon::parse('2025-08-22 08:30:00');
        $this->assertFalse($period->contains($beforeTime), 'Expected period to not contain time before start (08:30)');

        // Test time at start of period
        $startTime = Carbon::parse('2025-08-22 09:00:00');
        $this->assertTrue($period->contains($startTime), 'Expected period to contain time at start (09:00)');

        // Test time within period
        $withinTime = Carbon::parse('2025-08-22 12:30:00');
        $this->assertTrue($period->contains($withinTime), 'Expected period to contain time within period (12:30)');

        // Test time at end of period
        $endTime = Carbon::parse('2025-08-22 17:00:00');
        $this->assertTrue($period->contains($endTime), 'Expected period to contain time at end (17:00)');

        // Test time after the period
        $afterTime = Carbon::parse('2025-08-22 18:30:00');
        $this->assertFalse($period->contains($afterTime), 'Expected period to not contain time after end (18:30)');
    }

    public function testDailyPeriodSameHourContains(): void
    {
        // Same hour case test: 12:00 <= x <= 12:00
        $from = new TimeEdge();
        $from->hour = '12:00';

        $to = new TimeEdge();
        $to->hour = '12:00';

        $period = new DailyPeriod();
        $period->from = $from;
        $period->to = $to;

        // Test time before the exact hour
        $beforeTime = Carbon::parse('2025-08-22 11:59:00');
        $this->assertFalse($period->contains($beforeTime), 'Expected period to not contain time before exact hour (11:59)');

        // Test exact time
        $exactTime = Carbon::parse('2025-08-22 12:00:00');
        $this->assertTrue($period->contains($exactTime), 'Expected period to contain exact time (12:00)');

        // Test time after the exact hour
        $afterTime = Carbon::parse('2025-08-22 12:01:00');
        $this->assertFalse($period->contains($afterTime), 'Expected period to not contain time after exact hour (12:01)');
    }

    public function testDailyPeriodExternalContains(): void
    {
        // External case test: crosses midnight 22:00 <= x <= 06:00
        $from = new TimeEdge();
        $from->hour = '22:00';

        $to = new TimeEdge();
        $to->hour = '06:00';

        $period = new DailyPeriod();
        $period->from = $from;
        $period->to = $to;

        // Test time in the evening part (after 22:00)
        $eveningTime = Carbon::parse('2025-08-22 23:30:00');
        $this->assertTrue($period->contains($eveningTime), 'Expected period to contain evening time (23:30)');

        // Test time at start of period
        $startTime = Carbon::parse('2025-08-22 22:00:00');
        $this->assertTrue($period->contains($startTime), 'Expected period to contain time at start (22:00)');

        // Test time in the morning part (before 06:00)
        $morningTime = Carbon::parse('2025-08-22 03:30:00');
        $this->assertTrue($period->contains($morningTime), 'Expected period to contain morning time (03:30)');

        // Test time at end of period
        $endTime = Carbon::parse('2025-08-22 06:00:00');
        $this->assertTrue($period->contains($endTime), 'Expected period to contain time at end (06:00)');

        // Test time in the middle of the day (excluded)
        $middayTime = Carbon::parse('2025-08-22 12:00:00');
        $this->assertFalse($period->contains($middayTime), 'Expected period to not contain midday time (12:00)');

        // Test time just before start
        $beforeStartTime = Carbon::parse('2025-08-22 21:59:00');
        $this->assertFalse($period->contains($beforeStartTime), 'Expected period to not contain time just before start (21:59)');

        // Test time just after end
        $afterEndTime = Carbon::parse('2025-08-22 06:01:00');
        $this->assertFalse($period->contains($afterEndTime), 'Expected period to not contain time just after end (06:01)');
    }

    public function testDailyPeriodContainsNow(): void
    {
        $now = Carbon::now();

        $future = $now->copy()->addMinutes(2);
        $future2 = $now->copy()->addMinutes(4);
        $past = $now->copy()->subMinutes(2);
        $past2 = $now->copy()->subMinutes(4);

        $futureHour = $future->format('H:i');
        $future2Hour = $future2->format('H:i');
        $pastHour = $past->format('H:i');
        $past2Hour = $past2->format('H:i');

        // Test internal case: past <= now <= future
        $from = new TimeEdge();
        $from->hour = $pastHour;

        $to = new TimeEdge();
        $to->hour = $futureHour;

        $period = new DailyPeriod();
        $period->from = $from;
        $period->to = $to;

        $this->assertTrue($period->containsNow(), 'Expected period to contain now - internal case');

        // Test future case: both times are in the future
        $from = new TimeEdge();
        $from->hour = $futureHour;

        $to = new TimeEdge();
        $to->hour = $future2Hour;

        $period2 = new DailyPeriod();
        $period2->from = $from;
        $period2->to = $to;

        $this->assertFalse($period2->containsNow(), 'Expected period2 to not contain now - future case');

        // Test past case: both times are in the past
        $from = new TimeEdge();
        $from->hour = $past2Hour;

        $to = new TimeEdge();
        $to->hour = $pastHour;

        $period3 = new DailyPeriod();
        $period3->from = $from;
        $period3->to = $to;

        $this->assertFalse($period3->containsNow(), 'Expected period3 to not contain now - past case');

        // Test external case (crossing midnight): future <= now <= past (inverted)
        $from = new TimeEdge();
        $from->hour = $futureHour;

        $to = new TimeEdge();
        $to->hour = $pastHour;

        $period4 = new DailyPeriod();
        $period4->from = $from;
        $period4->to = $to;

        $this->assertFalse($period4->containsNow(), 'Expected period4 to not contain now - external case');

        // Test external case right (crossing midnight): future2 <= now <= future
        $from = new TimeEdge();
        $from->hour = $future2Hour;

        $to = new TimeEdge();
        $to->hour = $futureHour;

        $period5 = new DailyPeriod();
        $period5->from = $from;
        $period5->to = $to;

        $this->assertTrue($period5->containsNow(), 'Expected period5 to contain now - external case right');

        // Test external case left (crossing midnight): past <= now <= past2
        $from = new TimeEdge();
        $from->hour = $pastHour;

        $to = new TimeEdge();
        $to->hour = $past2Hour;

        $period6 = new DailyPeriod();
        $period6->from = $from;
        $period6->to = $to;

        $this->assertTrue($period6->containsNow(), 'Expected period6 to contain now - external case left');
    }

    public function testDailyPeriodContainsWithMinutes(): void
    {
        // Test with specific minutes
        $from = new TimeEdge();
        $from->hour = '10:15';

        $to = new TimeEdge();
        $to->hour = '10:45';

        $period = new DailyPeriod();
        $period->from = $from;
        $period->to = $to;

        // Test time before the period
        $beforeTime = Carbon::parse('2025-08-22 10:14:59');
        $this->assertFalse($period->contains($beforeTime), 'Expected period to not contain time just before start (10:14:59)');

        // Test time at start of period
        $startTime = Carbon::parse('2025-08-22 10:15:00');
        $this->assertTrue($period->contains($startTime), 'Expected period to contain time at start (10:15:00)');

        // Test time within period
        $withinTime = Carbon::parse('2025-08-22 10:30:00');
        $this->assertTrue($period->contains($withinTime), 'Expected period to contain time within period (10:30:00)');

        // Test time at end of period
        $endTime = Carbon::parse('2025-08-22 10:45:00');
        $this->assertTrue($period->contains($endTime), 'Expected period to contain time at end (10:45:00)');

        // Test time after the period
        $afterTime = Carbon::parse('2025-08-22 10:45:01');
        $this->assertFalse($period->contains($afterTime), 'Expected period to not contain time just after end (10:45:01)');
    }
}