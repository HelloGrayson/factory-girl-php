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
            FieldDef::past()->days(2)->months(1)->years(3)->get(),
            'Error getting unix timestamp'
        );
        $this->assertEquals(
            $time,
            FieldDef::past()->days(2)->months(1)->years(3)->get(DateIntervalHelper::DATE_TIME),
            'Error getting date time'
        );
        $this->assertEquals(
            $time->format('d-m-y'),
            FieldDef::past()->days(2)->months(1)->years(3)->get(DateIntervalHelper::DATE_STRING),
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
            FieldDef::future()->years(3)->months(1)->days(2)->get());
        $this->assertEquals(
            $time,
            FieldDef::future()->years(3)->months(1)->days(2)->get(DateIntervalHelper::DATE_TIME));
        $this->assertEquals(
            $time->format('d-m-y'),
            FieldDef::future()->years(3)->months(1)->days(2)->get(DateIntervalHelper::DATE_STRING));

    }

}
