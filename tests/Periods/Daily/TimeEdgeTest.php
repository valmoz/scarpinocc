<?php

namespace Scarpinocc\Test\Periods\Daily;

use PHPUnit\Framework\TestCase;
use Scarpinocc\Periods\Daily\TimeEdge;
use Carbon\Carbon;

class TimeEdgeTest extends TestCase
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

    public function testTimeEdgeBefore(): void
    {
        $baseTime = Carbon::parse('2025-08-22 12:00:00');

        // Test edge before the time
        $earlyEdge = new TimeEdge();
        $earlyEdge->hour = '10:00';
        $this->assertTrue($earlyEdge->before($baseTime), 'Expected edge (10:00) to be before the base time (12:00)');

        // Test edge after the time
        $lateEdge = new TimeEdge();
        $lateEdge->hour = '14:00';
        $this->assertFalse($lateEdge->before($baseTime), 'Expected edge (14:00) to not be before the base time (12:00)');

        // Test edge at the same time
        $sameEdge = new TimeEdge();
        $sameEdge->hour = '12:00';
        $this->assertFalse($sameEdge->before($baseTime), 'Expected edge (12:00) to not be before the same base time (12:00)');

        // Test with minutes
        $earlyMinuteEdge = new TimeEdge();
        $earlyMinuteEdge->hour = '11:59';
        $this->assertTrue($earlyMinuteEdge->before($baseTime), 'Expected edge (11:59) to be before the base time (12:00)');

        $lateMinuteEdge = new TimeEdge();
        $lateMinuteEdge->hour = '12:01';
        $this->assertFalse($lateMinuteEdge->before($baseTime), 'Expected edge (12:01) to not be before the base time (12:00)');
    }

    public function testTimeEdgeAfter(): void
    {
        $baseTime = Carbon::parse('2025-08-22 12:00:00');

        // Test edge after the time
        $lateEdge = new TimeEdge();
        $lateEdge->hour = '14:00';
        $this->assertTrue($lateEdge->after($baseTime), 'Expected edge (14:00) to be after the base time (12:00)');

        // Test edge before the time
        $earlyEdge = new TimeEdge();
        $earlyEdge->hour = '10:00';
        $this->assertFalse($earlyEdge->after($baseTime), 'Expected edge (10:00) to not be after the base time (12:00)');

        // Test edge at the same time
        $sameEdge = new TimeEdge();
        $sameEdge->hour = '12:00';
        $this->assertFalse($sameEdge->after($baseTime), 'Expected edge (12:00) to not be after the same base time (12:00)');

        // Test with minutes
        $lateMinuteEdge = new TimeEdge();
        $lateMinuteEdge->hour = '12:01';
        $this->assertTrue($lateMinuteEdge->after($baseTime), 'Expected edge (12:01) to be after the base time (12:00)');

        $earlyMinuteEdge = new TimeEdge();
        $earlyMinuteEdge->hour = '11:59';
        $this->assertFalse($earlyMinuteEdge->after($baseTime), 'Expected edge (11:59) to not be after the base time (12:00)');
    }

    public function testTimeEdgeEqual(): void
    {
        $baseTime = Carbon::parse('2025-08-22 12:00:00');

        // Test edge equal to the time
        $sameEdge = new TimeEdge();
        $sameEdge->hour = '12:00';
        $this->assertTrue($sameEdge->equals($baseTime), 'Expected edge (12:00) to be equal to the base time (12:00)');

        // Test edge before the time
        $earlyEdge = new TimeEdge();
        $earlyEdge->hour = '11:59';
        $this->assertFalse($earlyEdge->equals($baseTime), 'Expected edge (11:59) to not be equal to the base time (12:00)');

        // Test edge after the time
        $lateEdge = new TimeEdge();
        $lateEdge->hour = '12:01';
        $this->assertFalse($lateEdge->equals($baseTime), 'Expected edge (12:01) to not be equal to the base time (12:00)');

        // Test with different hours
        $differentEdge = new TimeEdge();
        $differentEdge->hour = '15:30';
        $this->assertFalse($differentEdge->equals($baseTime), 'Expected edge (15:30) to not be equal to the base time (12:00)');
    }

    public function testTimeEdgeBeforeOrEqual(): void
    {
        $baseTime = Carbon::parse('2025-08-22 12:00:00');

        // Test edge before the time
        $earlyEdge = new TimeEdge();
        $earlyEdge->hour = '10:00';
        $this->assertTrue($earlyEdge->beforeOrEquals($baseTime), 'Expected edge (10:00) to be before or equal to the base time (12:00)');

        // Test edge equal to the time
        $sameEdge = new TimeEdge();
        $sameEdge->hour = '12:00';
        $this->assertTrue($sameEdge->beforeOrEquals($baseTime), 'Expected edge (12:00) to be before or equal to the base time (12:00)');

        // Test edge after the time
        $lateEdge = new TimeEdge();
        $lateEdge->hour = '14:00';
        $this->assertFalse($lateEdge->beforeOrEquals($baseTime), 'Expected edge (14:00) to not be before or equal to the base time (12:00)');

        // Test with minutes - before
        $earlyMinuteEdge = new TimeEdge();
        $earlyMinuteEdge->hour = '11:59';
        $this->assertTrue($earlyMinuteEdge->beforeOrEquals($baseTime), 'Expected edge (11:59) to be before or equal to the base time (12:00)');

        // Test with minutes - after
        $lateMinuteEdge = new TimeEdge();
        $lateMinuteEdge->hour = '12:01';
        $this->assertFalse($lateMinuteEdge->beforeOrEquals($baseTime), 'Expected edge (12:01) to not be before or equal to the base time (12:00)');
    }

    public function testTimeEdgeAfterOrEqual(): void
    {
        $baseTime = Carbon::parse('2025-08-22 12:00:00');

        // Test edge after the time
        $lateEdge = new TimeEdge();
        $lateEdge->hour = '14:00';
        $this->assertTrue($lateEdge->afterOrEquals($baseTime), 'Expected edge (14:00) to be after or equal to the base time (12:00)');

        // Test edge equal to the time
        $sameEdge = new TimeEdge();
        $sameEdge->hour = '12:00';
        $this->assertTrue($sameEdge->afterOrEquals($baseTime), 'Expected edge (12:00) to be after or equal to the base time (12:00)');

        // Test edge before the time
        $earlyEdge = new TimeEdge();
        $earlyEdge->hour = '10:00';
        $this->assertFalse($earlyEdge->afterOrEquals($baseTime), 'Expected edge (10:00) to not be after or equal to the base time (12:00)');

        // Test with minutes - after
        $lateMinuteEdge = new TimeEdge();
        $lateMinuteEdge->hour = '12:01';
        $this->assertTrue($lateMinuteEdge->afterOrEquals($baseTime), 'Expected edge (12:01) to be after or equal to the base time (12:00)');

        // Test with minutes - before
        $earlyMinuteEdge = new TimeEdge();
        $earlyMinuteEdge->hour = '11:59';
        $this->assertFalse($earlyMinuteEdge->afterOrEquals($baseTime), 'Expected edge (11:59) to not be after or equal to the base time (12:00)');
    }
}