<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

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
        
        $this->assertContains($person, $ship->getCrew());
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
        
        $this->assertInstanceOf(TestEntity\Person::class, $this->factory->get('Badge')->getOwner());
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
        
        $this->assertContains($p1, $ship->getCrew());
        $this->assertContains($p2, $ship->getCrew());
    }
}
