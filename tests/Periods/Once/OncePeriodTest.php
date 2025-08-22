<?php

namespace Scarpinocc\Test\Periods\Once;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Once\OncePeriod;
use Scarpinocc\Periods\Once\TimestampEdge;

class OncePeriodTest extends TestCase
{
    public function testOncePeriodContains()
    {
        $t = Carbon::parse("2025-04-28 12:00");
        $past = Carbon::parse("2025-04-27 12:00");
        $future = Carbon::parse("2025-04-29 12:00");

        $let = Carbon::parse("2025-04-28 09:00");
        $ret = Carbon::parse("2025-04-28 18:00");

        $leftEdge = new TimestampEdge();
        $leftEdge->timestamp = $let;

        $rightEdge = new TimestampEdge();
        $rightEdge->timestamp = $ret;

        $period = new OncePeriod();
        $period->from = $leftEdge;
        $period->to = $rightEdge;

        $this->assertFalse($period->contains($past), "Expected period to not contain the past timestamp");
        $this->assertTrue($period->contains($let), "Expected period to contain the from timestamp");
        $this->assertTrue($period->contains($t), "Expected period to contain the contained timestamp");
        $this->assertTrue($period->contains($ret), "Expected period to contain the to timestamp");
        $this->assertFalse($period->contains($future), "Expected period to not contain the future timestamp");
    }

    public function testOncePeriodContainsNow()
    {
        $p2 = Carbon::now()->add(CarbonInterval::days(-2));
        $p1 = Carbon::now()->add(CarbonInterval::days(-1));
        $f1 = Carbon::now()->add(CarbonInterval::days(1));
        $f2 = Carbon::now()->add(CarbonInterval::days(2));

        $leftEdge = new TimestampEdge();
        $leftEdge->timestamp = $p1;

        $rightEdge = new TimestampEdge();
        $rightEdge->timestamp = $f1;

        $period = new OncePeriod();
        $period->from = $leftEdge;
        $period->to = $rightEdge;

        $this->assertTrue($period->containsNow(), "Expected period to contain the current timestamp");

        $leftEdge->timestamp = $p2;
        $rightEdge->timestamp = $p1;

        $this->assertFalse($period->containsNow(), "Expected past period to not contain the current timestamp");

        $leftEdge->timestamp = $f1;
        $rightEdge->timestamp = $f2;

        $this->assertFalse($period->containsNow(), "Expected future period to not contain the current timestamp");
    }
}