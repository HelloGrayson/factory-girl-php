<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use PHPUnit\Framework;
use FactoryGirl\Tests\Provider\Doctrine\TestDb;
use FactoryGirl\Provider\Doctrine\FixtureFactory;
use Doctrine\ORM\EntityManager;
use Exception;

abstract class TestCase extends Framework\TestCase
{
    /**
     * @var TestDb
     */
    protected $testDb;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Public to allow access from the broken 5.3 closures.
     *
     * @var FixtureFactory
     */
    public $factory;

    protected function setUp()
    {
        parent::setUp();

        $here = dirname(__FILE__);

        $this->testDb = new TestDb(
            $here . '/TestEntity',
            $here . '/TestProxy',
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestProxy'
        );

        $this->em = $this->testDb->createEntityManager();

        $this->factory = new FixtureFactory($this->em);
        $this->factory->setEntityNamespace('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity');
    }
}
