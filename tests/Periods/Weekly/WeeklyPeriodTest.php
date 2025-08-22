<?php

namespace Scarpinocc\Test\Periods\Weekly;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Weekly\DayTimeEdge;
use Scarpinocc\Periods\Weekly\WeeklyPeriod;

class WeeklyPeriodTest extends TestCase
{
    protected static $initialized = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!self::$initialized) {
            date_default_timezone_set('Europe/Rome');
            self::$initialized = true;
        }
    }

    public function testWeeklyPeriodInternalContains()
    {
        // internal case test
        // tuesday 05:35 < x < thursday 22:22
        $from = new DayTimeEdge();
        $from->day = 2; // tuesday
        $from->hour = "07:35";

        $to = new DayTimeEdge();
        $to->day = 4; // thursday
        $to->hour = "22:22";

        $period = new WeeklyPeriod();
        $period->from = $from;
        $period->to = $to;

        $t1 = Carbon::parse("2025-04-30 08:00:00");
        $this->assertTrue($period->contains($t1), "Expected period to contain the timestamp 1 - contained case");

        $t2 = Carbon::parse("2025-04-27 07:00:00");
        $this->assertFalse($period->contains($t2), "Expected period to not contain the timestamp 2 - excluded left case");

        $t3 = Carbon::parse("2025-05-02 07:00:00");
        $this->assertFalse($period->contains($t3), "Expected period to not contain the timestamp 3 - excluded right case");

        $t4 = Carbon::parse("2025-04-23 07:00:00");
        $this->assertTrue($period->contains($t4), "Expected period to contain the timestamp 4 - previous week contained case");

        $t5 = Carbon::parse("2025-05-08 21:00:00");
        $this->assertTrue($period->contains($t5), "Expected period to contain the timestamp 5 - next week contained case");

        $t6 = Carbon::parse("2025-04-21 07:35:00");
        $this->assertFalse($period->contains($t6), "Expected period to not contain the timestamp 6 - previous week excluded left case");

        $t7 = Carbon::parse("2025-05-09 07:00:00");
        $this->assertFalse($period->contains($t7), "Expected period to not contain the timestamp 7 - next week excluded right case");

        $t8 = Carbon::parse("2025-04-29 07:34:59");
        $this->assertFalse($period->contains($t8), "Expected period to not contain the timestamp 8 - 1s excluded left case");

        $t9 = Carbon::parse("2025-04-29 07:35:00");
        $this->assertTrue($period->contains($t9), "Expected period to contain the timestamp 9 - left edge case");

        $t10 = Carbon::parse("2025-04-29 07:35:01");
        $this->assertTrue($period->contains($t10), "Expected period to contain the timestamp 10 - 1s included left case");

        $t11 = Carbon::parse("2025-05-01 22:21:59");
        $this->assertTrue($period->contains($t11), "Expected period to contain the timestamp 11 - 1s included right case");

        $t12 = Carbon::parse("2025-05-01 22:22:00");
        $this->assertTrue($period->contains($t12), "Expected period to contain the timestamp 12 - right edge case");

        $t13 = Carbon::parse("2025-05-01 22:22:01");
        $this->assertFalse($period->contains($t13), "Expected period to not contain the timestamp 13 - 1s excluded right case");
    }

    public function testWeeklyPeriodInternalSameDayContains()
    {
        // same day internal case test
        // wednesday 09:00 <= x <= wednesday 18:00
        $from = new DayTimeEdge();
        $from->day = 3; // wednesday
        $from->hour = "09:00";

        $to = new DayTimeEdge();
        $to->day = 3; // wednesday
        $to->hour = "18:00";

        $period = new WeeklyPeriod();
        $period->from = $from;
        $period->to = $to;

        $t1 = Carbon::parse("2025-04-30 10:00:00");
        $this->assertTrue($period->contains($t1), "Expected period to contain the timestamp 1 - contained case");

        $t2 = Carbon::parse("2025-04-27 07:00:00");
        $this->assertFalse($period->contains($t2), "Expected period to not contain the timestamp 2 - excluded left case");

        $t3 = Carbon::parse("2025-05-02 07:00:00");
        $this->assertFalse($period->contains($t3), "Expected period to not contain the timestamp 3 - excluded right case");

        $t4 = Carbon::parse("2025-04-23 11:00:00");
        $this->assertTrue($period->contains($t4), "Expected period to contain the timestamp 4 - previous week contained case");

        $t5 = Carbon::parse("2025-05-07 16:30:00");
        $this->assertTrue($period->contains($t5), "Expected period to contain the timestamp 5 - next week contained case");

        $t6 = Carbon::parse("2025-04-21 07:35:00");
        $this->assertFalse($period->contains($t6), "Expected period to not contain the timestamp 6 - previous week excluded left case");

        $t7 = Carbon::parse("2025-05-09 07:00:00");
        $this->assertFalse($period->contains($t7), "Expected period to not contain the timestamp 7 - next week excluded right case");

        $t8 = Carbon::parse("2025-05-30 08:59:59");
        $this->assertFalse($period->contains($t8), "Expected period to not contain the timestamp 8 - 1s excluded left case");

        $t9 = Carbon::parse("2025-04-30 09:00:00");
        $this->assertTrue($period->contains($t9), "Expected period to contain the timestamp 9 - left edge case");

        $t10 = Carbon::parse("2025-04-30 09:00:01");
        $this->assertTrue($period->contains($t10), "Expected period to contain the timestamp 10 - 1s included left case");

        $t11 = Carbon::parse("2025-04-30 17:59:59");
        $this->assertTrue($period->contains($t11), "Expected period to contain the timestamp 11 - 1s included right case");

        $t12 = Carbon::parse("2025-04-30 18:00:00");
        $this->assertTrue($period->contains($t12), "Expected period to contain the timestamp 12 - right edge case");

        $t13 = Carbon::parse("2025-04-30 18:00:01");
        $this->assertFalse($period->contains($t13), "Expected period to not contain the timestamp 13 - 1s excluded right case");
    }

    public function testWeeklyPeriodExternalContains()
    {
        // external case test
        // x <= tuesday 05:35 || thursday 22:22 >= x
        // equals to: thursday 22:22 <= x <= tuesday 05:35
        $from = new DayTimeEdge();
        $from->day = 4; // thursday
        $from->hour = "22:22";

        $to = new DayTimeEdge();
        $to->day = 2; // tuesday
        $to->hour = "07:35";

        $period = new WeeklyPeriod();
        $period->from = $from;
        $period->to = $to;

        $t1 = Carbon::parse("2025-04-30 08:00:00");
        $this->assertFalse($period->contains($t1), "Expected period to not contain the timestamp 1 - excluded case");

        $t2 = Carbon::parse("2025-04-27 07:00:00");
        $this->assertTrue($period->contains($t2), "Expected period to contain the timestamp 2 - included left case");

        $t3 = Carbon::parse("2025-05-02 07:00:00");
        $this->assertTrue($period->contains($t3), "Expected period to contain the timestamp 3 - included right case");

        $t4 = Carbon::parse("2025-04-23 07:00:00");
        $this->assertFalse($period->contains($t4), "Expected period to not contain the timestamp 4 - previous week excluded case");

        $t5 = Carbon::parse("2025-05-08 21:00:00");
        $this->assertFalse($period->contains($t5), "Expected period to not contain the timestamp 5 - next week excluded case");

        $t6 = Carbon::parse("2025-04-21 07:35:00");
        $this->assertTrue($period->contains($t6), "Expected period to contain the timestamp 6 - previous week included left case");

        $t7 = Carbon::parse("2025-05-09 07:00:00");
        $this->assertTrue($period->contains($t7), "Expected period to contain the timestamp 7 - next week included right case");

        $t8 = Carbon::parse("2025-04-29 07:34:59");
        $this->assertTrue($period->contains($t8), "Expected period to contain the timestamp 8 - 1s included left case");

        $t9 = Carbon::parse("2025-04-29 07:35:00");
        $this->assertTrue($period->contains($t9), "Expected period to contain the timestamp 9 - left edge case");

        $t10 = Carbon::parse("2025-04-29 07:35:01");
        $this->assertFalse($period->contains($t10), "Expected period to not contain the timestamp 10 - 1s excluded left case");

        $t11 = Carbon::parse("2025-05-01 22:21:59");
        $this->assertFalse($period->contains($t11), "Expected period to not contain the timestamp 11 - 1s excluded right case");

        $t12 = Carbon::parse("2025-05-01 22:22:00");
        $this->assertTrue($period->contains($t12), "Expected period to contain the timestamp 12 - right edge case");

        $t13 = Carbon::parse("2025-05-01 22:22:01");
        $this->assertTrue($period->contains($t13), "Expected period to contain the timestamp 13 - 1s included right case");
    }

    public function testWeeklyPeriodExternalSameDayContains()
    {
        // same day external case test
        // wednesday 18:00 <= x <= wednesday 09:00
        $from = new DayTimeEdge();
        $from->day = 3; // wednesday
        $from->hour = "18:00";

        $to = new DayTimeEdge();
        $to->day = 3; // wednesday
        $to->hour = "09:00";

        $period = new WeeklyPeriod();
        $period->from = $from;
        $period->to = $to;

        $t1 = Carbon::parse("2025-04-30 10:00:00");
        $this->assertFalse($period->contains($t1), "Expected period to not contain the timestamp 1 - excluded case");

        $t2 = Carbon::parse("2025-04-27 07:00:00");
        $this->assertTrue($period->contains($t2), "Expected period to contain the timestamp 2 - included left case");

        $t3 = Carbon::parse("2025-05-02 07:00:00");
        $this->assertTrue($period->contains($t3), "Expected period to contain the timestamp 3 - included right case");

        $t4 = Carbon::parse("2025-04-23 11:00:00");
        $this->assertFalse($period->contains($t4), "Expected period to not contain the timestamp 4 - previous week excluded case");

        $t5 = Carbon::parse("2025-05-07 16:30:00");
        $this->assertFalse($period->contains($t5), "Expected period to not contain the timestamp 5 - next week excluded case");

        $t6 = Carbon::parse("2025-04-21 07:35:00");
        $this->assertTrue($period->contains($t6), "Expected period to contain the timestamp 6 - previous week included left case");

        $t7 = Carbon::parse("2025-05-09 07:00:00");
        $this->assertTrue($period->contains($t7), "Expected period to contain the timestamp 7 - next week included right case");

        $t8 = Carbon::parse("2025-05-30 08:59:59");
        $this->assertTrue($period->contains($t8), "Expected period to contain the timestamp 8 - 1s included left case");

        $t9 = Carbon::parse("2025-04-30 09:00:00");
        $this->assertTrue($period->contains($t9), "Expected period to contain the timestamp 9 - left edge case");

        $t10 = Carbon::parse("2025-04-30 09:00:01");
        $this->assertFalse($period->contains($t10), "Expected period to not contain the timestamp 10 - 1s excluded left case");

        $t11 = Carbon::parse("2025-04-30 17:59:59");
        $this->assertFalse($period->contains($t11), "Expected period to not contain the timestamp 11 - 1s excluded right case");

        $t12 = Carbon::parse("2025-04-30 18:00:00");
        $this->assertTrue($period->contains($t12), "Expected period to contain the timestamp 12 - right edge case");

        $t13 = Carbon::parse("2025-04-30 18:00:01");
        $this->assertTrue($period->contains($t13), "Expected period to contain the timestamp 13 - 1s included right case");
    }

    public function testWeeklyPeriodSameEdgeContains()
    {
        // same edge case test, valid only exactly in that moment.....
        // wednesday 18:00 <= x <= wednesday 18:00
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "18:00";

        $period = new WeeklyPeriod();
        $period->from = $edge;
        $period->to = $edge;

        $t1 = Carbon::parse("2025-04-30 17:59:59");
        $this->assertFalse($period->contains($t1), "Expected period to not contain the timestamp 1 - 1s excluded left case");

        $t2 = Carbon::parse("2025-04-30 18:00:00");
        $this->assertTrue($period->contains($t2), "Expected period to contain the timestamp 2 - edge case");

        $t3 = Carbon::parse("2025-04-30 18:00:01");
        $this->assertFalse($period->contains($t3), "Expected period to not contain the timestamp 3 - 1s excluded right case");

        $t4 = Carbon::parse("2025-05-07 18:00:00");
        $this->assertTrue($period->contains($t4), "Expected period to contain the timestamp 4 - next week edge case");

        $t5 = Carbon::parse("2025-04-23 18:00:00");
        $this->assertTrue($period->contains($t5), "Expected period to contain the timestamp 5 - previous week edge case");
    }

    public function testWeeklyPeriodContainsNow()
    {
        $now = Carbon::now();

        $future = $now->copy()->addMinutes(2);
        $future2 = $now->copy()->addMinutes(4);
        $past = $now->copy()->subMinutes(2);
        $past2 = $now->copy()->subMinutes(4);
        
        $futureDay = $future->dayOfWeek;
        $futureHour = $future->format("H:i");
        $future2Day = $future2->dayOfWeek;
        $future2Hour = $future2->format("H:i");
        $pastDay = $past->dayOfWeek;
        $pastHour = $past->format("H:i");
        $past2Day = $past2->dayOfWeek;
        $past2Hour = $past2->format("H:i");

        $period = new WeeklyPeriod();
        $period->from = new DayTimeEdge();
        $period->from->day = $pastDay;
        $period->from->hour = $pastHour;
        $period->to = new DayTimeEdge();
        $period->to->day = $futureDay;
        $period->to->hour = $futureHour;

        $this->assertTrue($period->containsNow(), "Expected period to contain now - internal case");

        $period2 = new WeeklyPeriod();
        $period2->from = new DayTimeEdge();
        $period2->from->day = $futureDay;
        $period2->from->hour = $futureHour;
        $period2->to = new DayTimeEdge();
        $period2->to->day = $future2Day;
        $period2->to->hour = $future2Hour;

        $this->assertFalse($period2->containsNow(), "Expected period2 to not contain now - past case");

        $period3 = new WeeklyPeriod();
        $period3->from = new DayTimeEdge();
        $period3->from->day = $past2Day;
        $period3->from->hour = $past2Hour;
        $period3->to = new DayTimeEdge();
        $period3->to->day = $pastDay;
        $period3->to->hour = $pastHour;

        $this->assertFalse($period3->containsNow(), "Expected period3 to not contain now - future case");

        $period4 = new WeeklyPeriod();
        $period4->from = $period->to;
        $period4->to = $period->from;

        $this->assertFalse($period4->containsNow(), "Expected period4 to not contain now - external case");

        $period5 = new WeeklyPeriod();
        $period5->from = $period2->to;
        $period5->to = $period2->from;

        $this->assertTrue($period5->containsNow(), "Expected period5 to contain now - external case right");

        $period6 = new WeeklyPeriod();
        $period6->from = $period3->to;
        $period6->to = $period3->from;

        $this->assertTrue($period6->containsNow(), "Expected period6 to contain now - external case left");
    }
}