<?php
namespace Xi\Doctrine\Fixtures;

class ExtraConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function canInvokeACallbackAfterObjectConstruction()
    {
        $this->factory->defineEntity('SpaceShip', array(
            'name' => 'Foo'
        ), array(
            'afterCreate' => function(TestEntity\SpaceShip $ss, array $fieldValues) {
                $ss->setName($ss->getName() . '-' . $fieldValues['name']);
            }
        ));
        $ss = $this->factory->get('SpaceShip');
        
        $this->assertEquals("Foo-Foo", $ss->getName());
    }
    
    /**
     * @test
     */
    public function theAfterCreateCallbackCanBeUsedToCallTheConstructor()
    {
        $this->factory->defineEntity('SpaceShip', array(
            'name' => 'Foo'
        ), array(
            'afterCreate' => function(TestEntity\SpaceShip $ss, array $fieldValues) {
                $ss->__construct($fieldValues['name'] . 'Master');
            }
        ));
        $ss = $this->factory->get('SpaceShip', array('name' => 'Xoo'));
        
        $this->assertTrue($ss->constructorWasCalled());
        $this->assertEquals('XooMaster', $ss->getName());
    }
}