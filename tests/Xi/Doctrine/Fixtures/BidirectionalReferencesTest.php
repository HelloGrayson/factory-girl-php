<?php
namespace Xi\Doctrine\Fixtures;

class BidirectionalReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function bidirectionalOntToManyReferencesAreAssignedBothWays()
    {
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person', array(
            'spaceShip' => FieldDef::reference('SpaceShip')
        ));
        
        $person = $this->factory->get('Person');
        $ship = $person->getSpaceShip();
        
        $this->assertTrue($ship->getCrew()->contains($person));
    }
    
    /**
     * @test
     */
    public function unidirectionalReferencesWorkAsUsual()
    {
        $this->factory->defineEntity('Badge', array(
            'owner' => FieldDef::reference('Person')
        ));
        $this->factory->defineEntity('Person');
        
        $this->assertTrue($this->factory->get('Badge')->getOwner() instanceof TestEntity\Person);
    }
    
    /**
     * @test
     */
    public function whenTheOneSideIsASingletonItMayGetSeveralChildObjects()
    {
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person', array(
            'spaceShip' => FieldDef::reference('SpaceShip')
        ));
        
        $ship = $this->factory->getAsSingleton('SpaceShip');
        $p1 = $this->factory->get('Person');
        $p2 = $this->factory->get('Person');
        
        $this->assertTrue($ship->getCrew()->contains($p1));
        $this->assertTrue($ship->getCrew()->contains($p2));
    }
}