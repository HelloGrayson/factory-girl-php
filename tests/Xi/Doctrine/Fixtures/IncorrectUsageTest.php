<?php
namespace Xi\Doctrine\Fixtures;

class IncorrectUsageTest extends TestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $factory = $this->factory->defineEntity('SpaceShip');
        $this->assertThrows(function() use ($factory) {
            $factory->defineEntity('SpaceShip');
        });
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEvenClasses()
    {
        $self = $this;
        $this->assertThrows(function() use ($self) {
            $self->factory->defineEntity('NotAClass');
        });
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEntities()
    {
        $this->assertTrue(class_exists('Xi\Doctrine\Fixtures\TestEntity\NotAnEntity', true));
        
        $self = $this;
        $this->assertThrows(function() use ($self) {
            $self->factory->defineEntity('NotAnEntity');
        });
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $self = $this;
        $this->assertThrows(function() use ($self) {
            $self->factory->defineEntity('SpaceShip', array(
                'pieType' => 'blueberry'
            ));
        });
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $this->factory->defineEntity('SpaceShip', array('name' => 'Alpha'));
        
        $self = $this;
        $this->assertThrows(function() use ($self) {
            $self->factory->get('SpaceShip', array(
                'pieType' => 'blueberry'
            ));
        });
    }
}
