<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use FactoryGirl\Provider\Doctrine\FieldDef;

class BasicUsageTest extends TestCase
{
    /**
     * @test
     */
    public function acceptsConstantValuesInEntityDefinitions()
    {
        $ss = $this->factory
            ->defineEntity('SpaceShip', array(
                'name' => 'My BattleCruiser'
            ))
            ->get('SpaceShip');
        
        $this->assertEquals('My BattleCruiser', $ss->getName());
    }
    
    /**
     * @test
     */
    public function acceptsGeneratorFunctionsInEntityDefinitions()
    {
        $name = "Star";
        $this->factory->defineEntity('SpaceShip', array(
            'name' => function() use (&$name) { return "M/S $name"; }
        ));
        
        $this->assertEquals('M/S Star', $this->factory->get('SpaceShip')->getName());
        $name = "Superstar";
        $this->assertEquals('M/S Superstar', $this->factory->get('SpaceShip')->getName());
    }
    
    /**
     * @test
     */
    public function valuesCanBeOverriddenAtCreationTime()
    {
        $ss = $this->factory
            ->defineEntity('SpaceShip', array(
                'name' => 'My BattleCruiser'
            ))
            ->get('SpaceShip', array('name' => 'My CattleBruiser'));
        $this->assertEquals('My CattleBruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function preservesDefaultValuesOfEntity()
    {
        $ss = $this->factory
            ->defineEntity('SpaceStation')
            ->get('SpaceStation');
        $this->assertEquals('Babylon5', $ss->getName());
    }    
    
    /**
     * @test
     */
    public function doesNotCallTheConstructorOfTheEntity()
    {
        $ss = $this->factory
            ->defineEntity('SpaceShip', array())
            ->get('SpaceShip');
        $this->assertFalse($ss->constructorWasCalled());
    }
    
    /**
     * @test
     */
    public function instantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified()
    {
        $ss = $this->factory
            ->defineEntity('SpaceShip', array(
                'name' => 'Battlestar Galaxy'
            ))
            ->get('SpaceShip');
        
        $this->assertTrue($ss->getCrew() instanceof ArrayCollection);
        $this->assertTrue($ss->getCrew()->isEmpty());
    }

    /**
     * @test
     */
    public function arrayElementsAreMappedToCollectionAsscociationFields()
    {
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person', array(
            'spaceShip' => FieldDef::reference('SpaceShip')
        ));

        $p1 = $this->factory->get('Person');
        $p2 = $this->factory->get('Person');

        $ship = $this->factory->get('SpaceShip', array(
            'name' => 'Battlestar Galaxy',
            'crew' => array($p1, $p2)
        ));
        
        $this->assertTrue($ship->getCrew() instanceof ArrayCollection);
        $this->assertTrue($ship->getCrew()->contains($p1));
        $this->assertTrue($ship->getCrew()->contains($p2));
    }
    
    /**
     * @test
     */
    public function unspecifiedFieldsAreLeftNull()
    {
        $this->factory->defineEntity('SpaceShip');
        $this->assertNull($this->factory->get('SpaceShip')->getName());
    }

    /**
     * @test
     */
    public function entityIsDefinedToDefaultNamespace()
    {
        $this->factory->defineEntity('SpaceShip');
        $this->factory->defineEntity('Person\User');

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip',
            get_class($this->factory->get('SpaceShip'))
        );

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Person\User',
            get_class($this->factory->get('Person\User'))
        );
    }

    /**
     * @test
     */
    public function entityCanBeDefinedToAnotherNamespace()
    {
        $this->factory->defineEntity(
            '\FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist'
        );

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist',
            get_class($this->factory->get(
                '\FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist'
            ))
        );
    }

    /**
     * @test
     */
    public function returnsListOfEntities()
    {
        $this->factory->defineEntity('SpaceShip');

        $this->assertCount(1, $this->factory->getList('SpaceShip'));
    }

    /**
     * @test
     */
    public function canSpecifyNumberOfReturnedInstances()
    {
        $this->factory->defineEntity('SpaceShip');

        $this->assertCount(5, $this->factory->getList('SpaceShip', array(), 5));
    }
}
