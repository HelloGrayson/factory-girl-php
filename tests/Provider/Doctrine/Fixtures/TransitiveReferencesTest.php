<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class TransitiveReferencesTest extends TestCase
{
    private function simpleSetup()
    {
        $this->factory->defineEntity('Person', [
            'spaceShip' => FieldDef::reference('SpaceShip'),
        ]);
        $this->factory->defineEntity('Badge', [
            'owner' => FieldDef::reference('Person')
        ]);
        $this->factory->defineEntity('SpaceShip');
    }
    
    /**
     * @test
     */
    public function referencesGetInstantiatedTransitively()
    {
        $this->simpleSetup();
        
        $badge = $this->factory->get('Badge');
        
        $this->assertNotNull($badge->getOwner()->getSpaceShip());
    }
    
    /**
     * @test
     */
    public function transitiveReferencesWorkWithSingletons()
    {
        $this->simpleSetup();
        
        $this->factory->getAsSingleton('SpaceShip');
        $badge1 = $this->factory->get('Badge');
        $badge2 = $this->factory->get('Badge');
        
        $this->assertNotSame($badge1->getOwner(), $badge2->getOwner());
        $this->assertSame($badge1->getOwner()->getSpaceShip(), $badge2->getOwner()->getSpaceShip());
    }
}
