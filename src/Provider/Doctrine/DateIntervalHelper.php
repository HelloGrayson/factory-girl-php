<?php

namespace FactoryGirl\Provider\Doctrine;


/**
 * Class DateIntervalHelper
 * @package FactoryGirl\Provider\Doctrine
 */
class DateIntervalHelper
{

    const DATE_TIME = 1;
    const TIMESTAMP = 2;
    const DATE_STRING = 3;

    /**
     * @var bool
     */
    public $negative;


    /**
     * @param \DateTime $time
     * @param bool|false $negative
     */
    public function __construct(\DateTime $time, $negative = false)
    {
        $this->time = $time;
        $this->negative = $negative;
    }

    /**
     * @param int $years
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function years($years)
    {
        $this->assertIntegerish($years);

        $this->modify(new \DateInterval('P'.$years.'Y'));
        
        return $this;
    }

    /**
     * @param int $months
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function months($months)
    {
        $this->assertIntegerish($months);

        $this->modify(new \DateInterval('P'.$months.'M'));
        
        return $this;
    }

    /**
     * @param int $days
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function days($days)
    {
        $this->assertIntegerish($days);

        $this->modify(new \DateInterval('P'.$days.'D'));
        
        return $this;
    }
    
    private function modify(\DateInterval $interval)
    {
        $interval->invert = (int) $this->negative;
        
        $this->time->add($interval);
    }

    /**
     * @param int $format
     * @return \DateTime|int|string
     */
    public function get($format = self::TIMESTAMP)
    {
        if ($format == self::DATE_TIME) {
            return $this->time;
        } 

        if ($format == self::TIMESTAMP) {
            return $this->time->getTimestamp();
        } 

        if ($format == self::DATE_STRING) {
            return $this->time->format('d-m-y');
        } 

        throw new \InvalidArgumentException("Unknown time format '". $format ."'");
    }

    /**
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    private function assertIntegerish($value)
    {
        if (!is_numeric($value) || $value != (int)$value) {
            throw new \InvalidArgumentException(sprintf(
                'Expected integer or integerish string, got "%s" instead.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
    }

}
