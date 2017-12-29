<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class PersistingTest extends TestCase
{
    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $this->factory->defineEntity('SpaceShip', array('name' => 'Zeta'));
        
        $this->factory->persistOnGet();
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();
        
        $this->assertNotNull($ss->getId());
        $this->assertTrue($this->em->contains($ss));
    }
    
    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $this->factory->defineEntity('SpaceShip', array('name' => 'Zeta'));
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();
        
        $this->assertNull($ss->getId());
        $this->assertFalse($this->em->contains($ss));
    }
}
