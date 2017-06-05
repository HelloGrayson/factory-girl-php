<?php

namespace FactoryGirl\Tests\Provider\Doctrine\ORM;

use FactoryGirl\Provider\Doctrine\ORM\Repository;
use FactoryGirl\Tests\Provider\Doctrine\ORM\TestEntity\User;

/**
 * @category   FactoryGirl
 * @package    Doctrine
 * @subpackage ORM
 * @author     Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause New BSD License
 */
class RepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new Repository(
            $this->em,
            $this->em->getClassMetadata('FactoryGirl\Tests\Provider\Doctrine\ORM\TestEntity\User')
        );
    }

    /**
     * @test
     */
    public function getsReference()
    {
        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $this->assertInstanceOf(
            'FactoryGirl\Tests\Provider\Doctrine\ORM\TestEntity\User',
            $this->repository->getReference($user->id)
        );
    }
}
