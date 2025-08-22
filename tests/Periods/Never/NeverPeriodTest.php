<?php

namespace Scarpinocc\Test\Periods\Never;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Never\NeverPeriod;

class NeverPeriodTest extends TestCase
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

    public function testNeverPeriodContains()
    {
        $period = new NeverPeriod();

        $testTime1 = Carbon::parse("2025-01-01 00:00:00");
        $this->assertFalse($period->contains($testTime1), "Expected NeverPeriod to not contain timestamp");
		}

    public function testNeverPeriodContainsNow()
    {
        $period = new NeverPeriod();
        $this->assertFalse($period->containsNow(), "Expected NeverPeriod to not contain current time");
		}
}