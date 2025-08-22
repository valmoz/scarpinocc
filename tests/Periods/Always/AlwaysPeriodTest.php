<?php

namespace Scarpinocc\Test\Periods\Always;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Always\AlwaysPeriod;

class AlwaysPeriodTest extends TestCase
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

    public function testAlwaysPeriodContains()
    {
        $period = new AlwaysPeriod();

        $testTime1 = Carbon::parse("2025-01-01 00:00:00");
        $this->assertTrue($period->contains($testTime1), "Expected AlwaysPeriod to contain timestamp");
    }

    public function testAlwaysPeriodContainsNow()
    {
        $period = new AlwaysPeriod();
        $this->assertTrue($period->containsNow(), "Expected AlwaysPeriod to contain current time");
    }
}