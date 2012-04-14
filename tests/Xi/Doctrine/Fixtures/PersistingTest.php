<?php
namespace Xi\Doctrine\Fixtures;

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
        $this->assertEquals($ss, $this->em->find('Xi\Doctrine\Fixtures\TestEntity\SpaceShip', $ss->getId()));
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
        $q = $this->em
            ->createQueryBuilder()
            ->select('ss')
            ->from('Xi\Doctrine\Fixtures\TestEntity\SpaceShip', 'ss')
            ->getQuery();
        $this->assertEmpty($q->getResult());
    }
    
}
