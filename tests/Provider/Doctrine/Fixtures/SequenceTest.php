<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class SequenceTest extends TestCase
{
    /**
     * @test
     */
    public function sequenceGeneratorCallsAFunctionWithAnIncrementingArgument()
    {
        $this->factory->defineEntity('SpaceShip', array(
            'name' => FieldDef::sequence(function ($n) {
                return "Alpha $n";
            })
        ));
        $this->assertSame('Alpha 1', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Alpha 2', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Alpha 3', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Alpha 4', $this->factory->get('SpaceShip')->getName());
    }
    
    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAPlaceholderString()
    {
        $this->factory->defineEntity('SpaceShip', array(
            'name' => FieldDef::sequence("Beta %d")
        ));
        $this->assertSame('Beta 1', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Beta 2', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Beta 3', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Beta 4', $this->factory->get('SpaceShip')->getName());
    }
    
    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAStringToAppendTo()
    {
        $this->factory->defineEntity('SpaceShip', array(
            'name' => FieldDef::sequence("Gamma ")
        ));
        $this->assertSame('Gamma 1', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Gamma 2', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Gamma 3', $this->factory->get('SpaceShip')->getName());
        $this->assertSame('Gamma 4', $this->factory->get('SpaceShip')->getName());
    }
}
