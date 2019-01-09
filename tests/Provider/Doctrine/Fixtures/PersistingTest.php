<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class PersistingTest extends TestCase
{
    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $this->factory->defineEntity('SpaceShip', ['name' => 'Zeta']);
        
        $this->factory->persistOnGet();
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();
        
        $this->assertNotNull($ss->getId());
        $this->assertSame($ss, $this->em->find('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip', $ss->getId()));
    }
    
    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $this->factory->defineEntity('SpaceShip', ['name' => 'Zeta']);
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();
        
        $this->assertNull($ss->getId());
        $q = $this->em
            ->createQueryBuilder()
            ->select('ss')
            ->from('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip', 'ss')
            ->getQuery();
        $this->assertEmpty($q->getResult());
    }
}
