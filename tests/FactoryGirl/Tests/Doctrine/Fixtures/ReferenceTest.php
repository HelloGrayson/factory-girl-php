<?php
namespace Xi\Doctrine\Fixtures;

class ReferenceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person', array(
            'name' => 'Eve',
            'spaceShip' => FieldDef::reference('SpaceShip')
        ));
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
        $person = $this->factory->get('Person', array('spaceShip' => null));
        
        $this->assertNull($person->getSpaceShip());
    }
}