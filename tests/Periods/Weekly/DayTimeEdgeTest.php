<?php

namespace Scarpinocc\Test\Periods\Weekly;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Weekly\DayTimeEdge;

class DayTimeEdgeTest extends TestCase
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
    public function testDayTimeEdgeBefore()
    {
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "12:00";
        $t = Carbon::parse("2025-04-30 12:00:00");
        $past = Carbon::parse("2025-04-28 12:00:00");
        $future = Carbon::parse("2025-05-01 12:00:00");
        $pastTime = Carbon::parse("2025-04-30 11:59:00");
        $futureTime = Carbon::parse("2025-04-30 12:01:00");
        $pastSecond = Carbon::parse("2025-04-30 11:59:01");
        $futureSecond = Carbon::parse("2025-04-30 12:00:01");

        $this->assertFalse($edge->before($past));
        $this->assertFalse($edge->before($pastTime));
        $this->assertFalse($edge->before($pastSecond));
        $this->assertFalse($edge->before($t));
        $this->assertTrue($edge->before($future));
        $this->assertTrue($edge->before($futureTime));
        $this->assertTrue($edge->before($futureSecond));

        $now = Carbon::now();
        $future1 = Carbon::now()->addMinutes(2);
        $past1 = Carbon::now()->addMinutes(-2);
        $futureDay = $future1->isoWeekday();
        $futureHour = $future1->format("H:i");
        $nowDay = $now->isoWeekday();
        $nowHour = $now->format("H:i");
        $pastDay = $past1->isoWeekday();
        $pastHour = $past1->format("H:i");

        $edge->day = $pastDay;
        $edge->hour = $pastHour;
        $this->assertTrue($edge->before($now));

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->before($now));

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertFalse($edge->before($correctedNow));
    }

    public function testDayTimeEdgeAfter()
    {
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "12:00";
        $t = Carbon::parse("2025-04-30 12:00:00");
        $past = Carbon::parse("2025-04-28 12:00:00");
        $future = Carbon::parse("2025-05-01 12:00:00");
        $pastTime = Carbon::parse("2025-04-30 11:59:00");
        $futureTime = Carbon::parse("2025-04-30 12:01:00");
        $pastSecond = Carbon::parse("2025-04-30 11:59:01");
        $futureSecond = Carbon::parse("2025-04-30 12:00:01");

        $this->assertTrue($edge->after($past));
        $this->assertTrue($edge->after($pastTime));
        $this->assertTrue($edge->after($pastSecond));
        $this->assertFalse($edge->after($t));
        $this->assertFalse($edge->after($future));
        $this->assertFalse($edge->after($futureTime));
        $this->assertFalse($edge->after($futureSecond));

        $now = Carbon::now();
        $future1 = Carbon::now()->addMinutes(2);
        $past1 = Carbon::now()->addMinutes(-2);
        $futureDay = $future1->isoWeekday();
        $futureHour = $future1->format("H:i");
        $nowDay = $now->isoWeekday();
        $nowHour = $now->format("H:i");
        $pastDay = $past1->isoWeekday();
        $pastHour = $past1->format("H:i");

        $edge->day = $pastDay;
        $edge->hour = $pastHour;
        $this->assertFalse($edge->after($now));

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertTrue($edge->after($now));

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertFalse($edge->after($correctedNow));
    }

    public function testDayTimeEdgeEquals()
    {
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "12:00";
        $t = Carbon::parse("2025-04-30 12:00:00");
        $past = Carbon::parse("2025-04-28 12:00:00");
        $future = Carbon::parse("2025-05-01 12:00:00");
        $pastTime = Carbon::parse("2025-04-30 11:59:00");
        $futureTime = Carbon::parse("2025-04-30 12:01:00");
        $pastSecond = Carbon::parse("2025-04-30 11:59:01");
        $futureSecond = Carbon::parse("2025-04-30 12:00:01");

        $this->assertFalse($edge->equals($past));
        $this->assertFalse($edge->equals($pastTime));
        $this->assertFalse($edge->equals($pastSecond));
        $this->assertTrue($edge->equals($t));
        $this->assertFalse($edge->equals($future));
        $this->assertFalse($edge->equals($futureTime));
        $this->assertFalse($edge->equals($futureSecond));

        $now = Carbon::now();
        $future1 = Carbon::now()->addMinutes(2);
        $past1 = Carbon::now()->addMinutes(-2);
        $futureDay = $future1->isoWeekday();
        $futureHour = $future1->format("H:i");
        $nowDay = $now->isoWeekday();
        $nowHour = $now->format("H:i");
        $pastDay = $past1->isoWeekday();
        $pastHour = $past1->format("H:i");

        $edge->day = $pastDay;
        $edge->hour = $pastHour;
        $this->assertFalse($edge->equals($now));

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->equals($now));

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->equals($correctedNow));

    }

    public function testDayTimeEdgeBeforeOrEquals()
    {
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "12:00";
        $t = Carbon::parse("2025-04-30 12:00:00");
        $past = Carbon::parse("2025-04-28 12:00:00");
        $future = Carbon::parse("2025-05-01 12:00:00");
        $pastTime = Carbon::parse("2025-04-30 11:59:00");
        $futureTime = Carbon::parse("2025-04-30 12:01:00");
        $pastSecond = Carbon::parse("2025-04-30 11:59:01");
        $futureSecond = Carbon::parse("2025-04-30 12:00:01");

        $this->assertFalse($edge->beforeOrEquals($past));
        $this->assertFalse($edge->beforeOrEquals($pastTime));
        $this->assertFalse($edge->beforeOrEquals($pastSecond));
        $this->assertTrue($edge->beforeOrEquals($t));
        $this->assertTrue($edge->beforeOrEquals($future));
        $this->assertTrue($edge->beforeOrEquals($futureTime));
        $this->assertTrue($edge->beforeOrEquals($futureSecond));

        $now = Carbon::now();
        $future1 = Carbon::now()->addMinutes(2);
        $past1 = Carbon::now()->addMinutes(-2);
        $futureDay = $future1->isoWeekday();
        $futureHour = $future1->format("H:i");
        $nowDay = $now->isoWeekday();
        $nowHour = $now->format("H:i");
        $pastDay = $past1->isoWeekday();
        $pastHour = $past1->format("H:i");

        $edge->day = $pastDay;
        $edge->hour = $pastHour;
        $this->assertTrue($edge->beforeOrEquals($now));

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->beforeOrEquals($now));

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->beforeOrEquals($correctedNow));
    }

    public function testDayTimeEdgeAfterOrEquals()
    {
        $edge = new DayTimeEdge();
        $edge->day = 3; // wednesday
        $edge->hour = "12:00";
        $t = Carbon::parse("2025-04-30 12:00:00");
        $past = Carbon::parse("2025-04-28 12:00:00");
        $future = Carbon::parse("2025-05-01 12:00:00");
        $pastTime = Carbon::parse("2025-04-30 11:59:00");
        $futureTime = Carbon::parse("2025-04-30 12:01:00");
        $pastSecond = Carbon::parse("2025-04-30 11:59:01");
        $futureSecond = Carbon::parse("2025-04-30 12:00:01");

        $this->assertTrue($edge->afterOrEquals($past));
        $this->assertTrue($edge->afterOrEquals($pastTime));
        $this->assertTrue($edge->afterOrEquals($pastSecond));
        $this->assertTrue($edge->afterOrEquals($t));
        $this->assertFalse($edge->afterOrEquals($future));
        $this->assertFalse($edge->afterOrEquals($futureTime));
        $this->assertFalse($edge->afterOrEquals($futureSecond));

        $now = Carbon::now();
        $future1 = Carbon::now()->addMinutes(2);
        $past1 = Carbon::now()->addMinutes(-2);
        $futureDay = $future1->isoWeekday();
        $futureHour = $future1->format("H:i");
        $nowDay = $now->isoWeekday();
        $nowHour = $now->format("H:i");
        $pastDay = $past1->isoWeekday();
        $pastHour = $past1->format("H:i");

        $edge->day = $pastDay;
        $edge->hour = $pastHour;
        $this->assertFalse($edge->afterOrEquals($now));

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertTrue($edge->afterOrEquals($now));

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->afterOrEquals($correctedNow));
    }

}