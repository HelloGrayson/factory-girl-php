<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

class IncorrectUsageTest extends TestCase
{
    /**
     * @test
     */
    public function throwsWhenTryingToDefineTheSameEntityTwice()
    {
        $factory = $this->factory->defineEntity('SpaceShip');
        
        $this->expectException(\Exception::class);
        
        $factory->defineEntity('SpaceShip');
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEvenClasses()
    {
        $this->expectException(\Exception::class);

        $this->factory->defineEntity('NotAClass');
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineEntitiesThatAreNotEntities()
    {
        $this->assertTrue(class_exists('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\NotAnEntity', true));

        $this->expectException(\Exception::class);
        
        $this->factory->defineEntity('NotAnEntity');
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToDefineNonexistentFields()
    {
        $this->expectException(\Exception::class);
        
        $this->factory->defineEntity('SpaceShip', [
            'pieType' => 'blueberry'
        ]);
    }
    
    /**
     * @test
     */
    public function throwsWhenTryingToGiveNonexistentFieldsWhileConstructing()
    {
        $this->factory->defineEntity('SpaceShip', ['name' => 'Alpha']);

        $this->expectException(\Exception::class);

        $this->factory->get('SpaceShip', [
            'pieType' => 'blueberry'
        ]);
    }

    /**
     * @test
     */
    public function throwsWhenTryingToGetLessThanOneInstance()
    {
        $this->factory->defineEntity('SpaceShip');

        $this->expectException(\Exception::class);

        $this->factory->getList('SpaceShip', [], 0);
    }
}
