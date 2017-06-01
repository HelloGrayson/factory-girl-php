<?php

namespace FactoryGirl\Tests\Provider\Doctrine\ORM;

use FactoryGirl\Provider\Doctrine\DateIntervalHelper;

class DateIntervalHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerInvalidIntegerish
     * 
     * @param mixed $years
     */
    public function testYearsRejectsInvalidValue($years)
    {
        $helper = new DateIntervalHelper(new \DateTime());
        
        $this->setExpectedException('RuntimeException');
        
        $helper->years($years);
    }

    /**
     * @dataProvider providerInvalidIntegerish
     * 
     * @param mixed $months
     */
    public function testMonthsRejectsInvalidValue($months)
    {
        $helper = new DateIntervalHelper(new \DateTime());
        
        $this->setExpectedException('RuntimeException');
        
        $helper->months($months);
    }

    /**
     * @dataProvider providerInvalidIntegerish
     * 
     * @param mixed $days
     */
    public function testDaysRejectsInvalidValue($days)
    {
        $helper = new DateIntervalHelper(new \DateTime());
        
        $this->setExpectedException('RuntimeException');
        
        $helper->days($days);
    }

    /**
     * @return array
     */
    public function providerInvalidIntegerish()
    {
        $values = [
            'array' => [],
            'boolean-false' => false,
            'boolean-true' => true,
            'float-negative' => -3.14,
            'float-negative-casted-to-string' => (string) -3.14,
            'float-positive' => 3.14,
            'float-positive-casted-to-string' => (string) 3.14,
            'null' => null,
            'object' => new \stdClass(),
            'resource' => fopen(__FILE__, 'r'),
            'string' => 'foo',
        ];
        
        return \array_map(function ($value) {
            return [
                $value,
            ];
        }, $values);
    }
}