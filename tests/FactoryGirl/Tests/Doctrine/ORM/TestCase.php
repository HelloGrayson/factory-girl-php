<?php

namespace Xi\Doctrine\ORM;

use PHPUnit_Framework_TestCase,
    Xi\Doctrine\TestDb;

/**
 * @category   Xi
 * @package    Doctrine
 * @subpackage ORM
 * @author     Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause New BSD License
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var TestDb
     */
    protected $testDb;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp()
    {
        parent::setUp();

        $here = dirname(__FILE__);

        $this->testDb = new TestDb(
            $here . '/TestEntity',
            $here . '/TestProxy',
            'Xi\Doctrine\ORM\TestProxy'
        );

        $this->em = $this->testDb->createEntityManager();
    }
}
