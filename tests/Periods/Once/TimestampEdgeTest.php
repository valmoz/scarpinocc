<?php

namespace Scarpinocc\Test\Periods\Once;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Once\TimestampEdge;

class TimestampEdgeTest extends TestCase
{

    public function testTimestampEdgeBefore()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertFalse($edge->before($past), "Expected edge to not be before the past timestamp");
        $this->assertFalse($edge->before($t), "Expected edge to not be before the same timestamp");
        $this->assertTrue($edge->before($future), "Expected edge to be before the future timestamp");
    }

    public function testTimestampEdgeAfter()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertTrue($edge->after($past), "Expected edge to be after the past timestamp");
        $this->assertFalse($edge->after($t), "Expected edge to not be after the same timestamp");
        $this->assertFalse($edge->after($future), "Expected edge to not be after the future timestamp");
    }

    public function testTimestampEdgeEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertFalse($edge->equals($past), "Expected edge not to be equal the past timestamp");
        $this->assertTrue($edge->equals($t), "Expected edge to be equal the same timestamp");
        $this->assertFalse($edge->equals($future), "Expected edge to not be equal the future timestamp");
    }

    public function testTimestampEdgeBeforeOrEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertFalse($edge->beforeOrEquals($past), "Expected edge to not be before or equal the past timestamp");
        $this->assertTrue($edge->beforeOrEquals($t), "Expected edge to not be before or equal the same timestamp");
        $this->assertTrue($edge->beforeOrEquals($future), "Expected edge to be before or equal the future timestamp");
    }

    public function testTimestampEdgeAfterOrEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertTrue($edge->afterOrEquals($past), "Expected edge to be after or equal the past timestamp");
        $this->assertTrue($edge->afterOrEquals($t), "Expected edge to be after or equal the same timestamp");
        $this->assertFalse($edge->afterOrEquals($future), "Expected edge to not be after or equal the future timestamp");
    }
}