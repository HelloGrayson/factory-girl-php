<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\DateIntervalHelper;
use FactoryGirl\Provider\Doctrine\FieldDef;

/**
 * Class TimeTest
 * @package FactoryGirl\Tests\Provider\Doctrine\Fixtures
 */
class TimeTest extends TestCase
{
    public function testGetTimePast()
    {
        $time = new \DateTime();
        $interval = new \DateInterval('P3Y1M2D');
        $interval->invert = 1;
        $time->add($interval);
        $this->assertEquals(
            $time->getTimestamp(),
            FieldDef::past()->years(3)->months(1)->days(2)->get(),
            'Error getting unix timestamp'
        );
        $this->assertEquals(
            $time->format('d-m-y'),
            FieldDef::past()->years(3)->months(1)->days(2)->get(DateIntervalHelper::DATE_STRING),
            'Error getting string'
        );
    }

    public function testGetTimeFuture()
    {
        $time = new \DateTime();
        $interval = new \DateInterval('P3Y1M2D');
        $time->add($interval);
        $this->assertEquals(
            $time->getTimestamp(),
            FieldDef::future()->years(3)->months(1)->days(2)->get()
        );
        $this->assertEquals(
            $time->format('d-m-y'),
            FieldDef::future()->years(3)->months(1)->days(2)->get(DateIntervalHelper::DATE_STRING)
        );
    }
}
