<?php

namespace Scarpinocc\Test;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Scarpinocc\Scarpinocc;
use Scarpinocc\Periods\PeriodInterface;
use Scarpinocc\Periods\Weekly\WeeklyPeriod;
use Scarpinocc\Periods\Once\OncePeriod;

class ScarpinoccTest extends TestCase
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

    public function testUnmarshal()
    {
        $exampleJson = '{
   "periods":[
      {
         "name":"scheduled maintainance",
         "description":"update indexes",
         "type":"weekly",
         "from":{
            "day":"saturday",
            "hour":"23:00"
         },
         "to":{
            "day":"sunday",
            "hour":"07:00"
         }
      },
      {
         "name":"service interruption",
         "description":"as defined in mail 18/02/2025",
         "type":"once",
         "from":{
            "timestamp":"2025-02-20 12:30:00"
         },
         "to":{
            "timestamp":"2025-02-20 14:30:00"
         }
      }
   ]
}';

        $scarpinocc = Scarpinocc::fromJson($exampleJson);
        $this->assertEquals(2, count($scarpinocc->periods), "Expected 2 periods to be unmarshalled");

        // Test weekly period
        $weeklyPeriod = $scarpinocc->periods[0];
        $this->assertInstanceOf(WeeklyPeriod::class, $weeklyPeriod);
        $this->assertEquals(6, $weeklyPeriod->from->day, "Expected Saturday (6)");
        $this->assertEquals("23:00", $weeklyPeriod->from->hour);
        $this->assertEquals(7, $weeklyPeriod->to->day, "Expected Sunday (7)");
        $this->assertEquals("07:00", $weeklyPeriod->to->hour);

        // Test once period
        $oncePeriod = $scarpinocc->periods[1];
        $this->assertInstanceOf(OncePeriod::class, $oncePeriod);
        $expectedFrom = Carbon::parse("2025-02-20 12:30:00");
        $expectedTo = Carbon::parse("2025-02-20 14:30:00");
        $this->assertTrue($oncePeriod->from->timestamp->equalTo($expectedFrom));
        $this->assertTrue($oncePeriod->to->timestamp->equalTo($expectedTo));
    }

    public function testContains()
    {
        $c1 = new Scarpinocc();
        $c1->periods = [
            new MockPeriod(true),
            new MockPeriod(true),
        ];
        $this->assertTrue($c1->contains(Carbon::now()), "Expected Contains to return true for c1");

        $c2 = new Scarpinocc();
        $c2->periods = [
            new MockPeriod(false),
            new MockPeriod(false),
        ];
        $this->assertFalse($c2->contains(Carbon::now()), "Expected Contains to return false for c2");

        $c3 = new Scarpinocc();
        $c3->periods = [
            new MockPeriod(false),
            new MockPeriod(true),
        ];
        $this->assertTrue($c3->contains(Carbon::now()), "Expected Contains to return true for c3");

        $c4 = new Scarpinocc();
        $c4->periods = [
            new MockPeriod(true),
            new MockPeriod(false),
        ];
        $this->assertTrue($c4->contains(Carbon::now()), "Expected Contains to return true for c4");

        $c5 = new Scarpinocc();
        $c5->periods = [];
        $this->assertFalse($c5->contains(Carbon::now()), "Expected Contains to return false for c5");

        $c6 = new Scarpinocc();
        $c6->periods = null;
        $this->assertFalse($c6->contains(Carbon::now()), "Expected Contains to return false for c6");
    }

    public function testContainsNow()
    {
        $c1 = new Scarpinocc();
        $c1->periods = [
            new MockPeriod(true),
            new MockPeriod(true),
        ];
        $this->assertTrue($c1->containsNow(), "Expected ContainsNow to return true for c1");

        $c2 = new Scarpinocc();
        $c2->periods = [
            new MockPeriod(false),
            new MockPeriod(false),
        ];
        $this->assertFalse($c2->containsNow(), "Expected ContainsNow to return false for c2");

        $c3 = new Scarpinocc();
        $c3->periods = [
            new MockPeriod(false),
            new MockPeriod(true),
        ];
        $this->assertTrue($c3->containsNow(), "Expected ContainsNow to return true for c3");

        $c4 = new Scarpinocc();
        $c4->periods = [
            new MockPeriod(true),
            new MockPeriod(false),
        ];
        $this->assertTrue($c4->containsNow(), "Expected ContainsNow to return true for c4");

        $c5 = new Scarpinocc();
        $c5->periods = [];
        $this->assertFalse($c5->containsNow(), "Expected ContainsNow to return false for c5");

        $c6 = new Scarpinocc();
        $c6->periods = null;
        $this->assertFalse($c6->containsNow(), "Expected ContainsNow to return false for c6");
    }
}

class MockPeriod implements PeriodInterface
{
    private $result;

    public function __construct(bool $result)
    {
        $this->result = $result;
    }

    public function contains(Carbon $t)
    {
        return $this->result;
    }

    public function containsNow()
    {
        return $this->result;
    }
}
