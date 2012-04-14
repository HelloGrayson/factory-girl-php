<?php

namespace FactoryGirl\Tests;

use FactoryGirl\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $subject;

    public function setUp()
    {
        $this->subject = new Factory();
    }

    public function testClassShouldExist()
    {
        $this->assertTrue(true);
    }
}