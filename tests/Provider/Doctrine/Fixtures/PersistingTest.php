<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class PersistingTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->factory->defineEntity('SpaceShip', array('name' => 'Zeta'));
    }

    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $this->factory->persistOnGet();

        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();

        $this->assertNotNull($ss->getId());
        $this->assertTrue($this->em->contains($ss));
    }

    /**
     * @test
     */
    public function createAlwaysPersists()
    {
        $ss = $this->factory->create('SpaceShip');
        $this->em->flush();

        $this->assertNotNull($ss->getId());
        $this->assertTrue($this->em->contains($ss));
    }

    /**
     * @test
     */
    public function buildNeverPersists()
    {
        $this->factory->persistOnGet();

        $ss = $this->factory->build('SpaceShip');
        $this->em->flush();

        $this->assertNull($ss->getId());
        $this->assertFalse($this->em->contains($ss));
    }

    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();

        $this->assertNull($ss->getId());
        $this->assertFalse($this->em->contains($ss));
    }
}
