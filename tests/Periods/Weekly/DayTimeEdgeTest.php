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

        $this->assertFalse($edge->before($past), "Expected edge to not be before the past timestamp");
        $this->assertFalse($edge->before($pastTime), "Expected edge to not be before the past time timestamp");
        $this->assertFalse($edge->before($pastSecond), "Expected edge to not be before the past second timestamp");
        $this->assertFalse($edge->before($t), "Expected edge to not be before the same timestamp");
        $this->assertTrue($edge->before($future), "Expected edge to be before the future timestamp");
        $this->assertTrue($edge->before($futureTime), "Expected edge to be before the future time timestamp");
        $this->assertTrue($edge->before($futureSecond), "Expected edge to be before the future second timestamp");

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
        $this->assertTrue($edge->before($now), "Expected edge to be before now");

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->before($now), "Expected edge to not be before now");

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertFalse($edge->before($correctedNow), "Expected edge to not be before now");
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

        $this->assertTrue($edge->after($past), "Expected edge to be after the past timestamp");
        $this->assertTrue($edge->after($pastTime), "Expected edge to be after the past time timestamp");
        $this->assertTrue($edge->after($pastSecond), "Expected edge to be after the past second timestamp");
        $this->assertFalse($edge->after($t), "Expected edge to not be after the same timestamp");
        $this->assertFalse($edge->after($future), "Expected edge to not be after the future timestamp");
        $this->assertFalse($edge->after($futureTime), "Expected edge to not be after the future time timestamp");
        $this->assertFalse($edge->after($futureSecond), "Expected edge to not be after the future second timestamp");

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
        $this->assertFalse($edge->after($now), "Expected edge to not be after now");

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertTrue($edge->after($now), "Expected edge to be after now");

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertFalse($edge->after($correctedNow), "Expected edge to not be after now");
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

        $this->assertFalse($edge->equals($past), "Expected edge to not be equal the past timestamp");
        $this->assertFalse($edge->equals($pastTime), "Expected edge to not be equal the past time timestamp");
        $this->assertFalse($edge->equals($pastSecond), "Expected edge to not be equal the past second timestamp");
        $this->assertTrue($edge->equals($t), "Expected edge to be equal the same timestamp");
        $this->assertFalse($edge->equals($future), "Expected edge to not be equal the future timestamp");
        $this->assertFalse($edge->equals($futureTime), "Expected edge to not be equal the future time timestamp");
        $this->assertFalse($edge->equals($futureSecond), "Expected edge to not be equal the future second timestamp");

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
        $this->assertFalse($edge->equals($now), "Expected edge to not be equal now");

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->equals($now), "Expected edge to not be equal now");

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->equals($correctedNow), "Expected edge to be equal now");

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

        $this->assertFalse($edge->beforeOrEquals($past), "Expected edge to not be before or equal the past timestamp");
        $this->assertFalse($edge->beforeOrEquals($pastTime), "Expected edge to not be before or equal the past time timestamp");
        $this->assertFalse($edge->beforeOrEquals($pastSecond), "Expected edge to not be before or equal the past second timestamp");
        $this->assertTrue($edge->beforeOrEquals($t), "Expected edge to be before or equal the same timestamp");
        $this->assertTrue($edge->beforeOrEquals($future), "Expected edge to be before or equal the future timestamp");
        $this->assertTrue($edge->beforeOrEquals($futureTime), "Expected edge to be before or equal the future time timestamp");
        $this->assertTrue($edge->beforeOrEquals($futureSecond), "Expected edge to be before or equal the future second timestamp");

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
        $this->assertTrue($edge->beforeOrEquals($now), "Expected edge to be before or equal now");

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertFalse($edge->beforeOrEquals($now), "Expected edge to not be before or equal now");

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->beforeOrEquals($correctedNow), "Expected edge to be before or equal now");
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

        $this->assertTrue($edge->afterOrEquals($past), "Expected edge to be after or equal the past timestamp");
        $this->assertTrue($edge->afterOrEquals($pastTime), "Expected edge to be after or equal the past time timestamp");
        $this->assertTrue($edge->afterOrEquals($pastSecond), "Expected edge to be after or equal the past second timestamp");
        $this->assertTrue($edge->afterOrEquals($t), "Expected edge to be after or equal the same timestamp");
        $this->assertFalse($edge->afterOrEquals($future), "Expected edge to not be after or equal the future timestamp");
        $this->assertFalse($edge->afterOrEquals($futureTime), "Expected edge to not be after or equal the future time timestamp");
        $this->assertFalse($edge->afterOrEquals($futureSecond), "Expected edge to not be after or equal the future second timestamp");

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
        $this->assertFalse($edge->afterOrEquals($now), "Expected edge to not be after or equal now");

        $edge->day = $futureDay;
        $edge->hour = $futureHour;
        $this->assertTrue($edge->afterOrEquals($now), "Expected edge to be after or equal now");

        $edge->day = $nowDay;
        $edge->hour = $nowHour;
        $nowStr = $now->format("Y-m-d H:i:00");
        $correctedNow = Carbon::parse($nowStr);
        $this->assertTrue($edge->afterOrEquals($correctedNow), "Expected edge to be after or equal now");
    }

}