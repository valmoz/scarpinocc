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

        $this->assertFalse($edge->before($past));
        $this->assertFalse($edge->before($t));
        $this->assertTrue($edge->before($future));
    }

    public function testTimestampEdgeAfter()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertTrue($edge->after($past));
        $this->assertFalse($edge->after($t));
        $this->assertFalse($edge->after($future));
    }

    public function testTimestampEdgeEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertFalse($edge->equals($past));
        $this->assertTrue($edge->equals($t));
        $this->assertFalse($edge->equals($future));
    }

    public function testTimestampEdgeBeforeOrEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertFalse($edge->beforeOrEquals($past));
        $this->assertTrue($edge->beforeOrEquals($t));
        $this->assertTrue($edge->beforeOrEquals($future));
    }

    public function testTimestampEdgeAfterOrEquals()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $edge = new TimestampEdge();
        $edge->timestamp = $t;

        $this->assertTrue($edge->afterOrEquals($past));
        $this->assertTrue($edge->afterOrEquals($t));
        $this->assertFalse($edge->afterOrEquals($future));
    }
}