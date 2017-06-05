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
     * @return $this
     */
    public function years($years)
    {
        if (!is_numeric($years)) {
            throw new \RuntimeException();
        }

        $this->modify(new \DateInterval('P'.$years.'Y'));
        
        return $this;
    }

    /**
     * @param int $months
     * @return $this
     */
    public function months($months)
    {
        if (!is_numeric($months)) {
            throw new \RuntimeException();
        }

        $this->modify(new \DateInterval('P'.$months.'M'));
        
        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function days($days)
    {
        if (!is_numeric($days)) {
            throw new \RuntimeException();
        }
        
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

}
