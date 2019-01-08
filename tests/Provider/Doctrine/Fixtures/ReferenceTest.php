<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class ReferenceTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person', [
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference('SpaceShip')
        ]);
    }
    
    /**
     * @test
     */
    public function referencedObjectShouldBeCreatedAutomatically()
    {
        $ss1 = $this->factory->get('Person')->getSpaceShip();
        $ss2 = $this->factory->get('Person')->getSpaceShip();
        
        $this->assertNotNull($ss1);
        $this->assertNotNull($ss2);
        $this->assertNotSame($ss1, $ss2);
    }
    
    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        $person = $this->factory->get('Person', ['spaceShip' => null]);
        
        $this->assertNull($person->getSpaceShip());
    }
}
