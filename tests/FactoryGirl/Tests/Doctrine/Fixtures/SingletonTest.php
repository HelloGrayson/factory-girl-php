<?php
namespace Xi\Doctrine\Fixtures;

class SingletonTest extends TestCase
{
    /**
     * @test
     */
    public function afterGettingAnEntityAsASingletonGettingTheEntityAgainReturnsTheSameObject()
    {
        $this->factory->defineEntity('SpaceShip');
        
        $ss = $this->factory->getAsSingleton('SpaceShip');
        
        $this->assertSame($ss, $this->factory->get('SpaceShip'));
        $this->assertSame($ss, $this->factory->get('SpaceShip'));
    }
    
    /**
     * @test
     */
    public function getAsSingletonMethodAcceptsFieldOverridesLikeGet()
    {
        $this->factory->defineEntity('SpaceShip');
        
        $ss = $this->factory->getAsSingleton('SpaceShip', array('name' => 'Beta'));
        $this->assertSame('Beta', $ss->getName());
        $this->assertSame('Beta', $this->factory->get('SpaceShip')->getName());
    }
    
    /**
     * @test
     */
    public function throwsAnErrorWhenCallingGetSingletonTwiceOnTheSameEntity()
    {
        $this->factory->defineEntity('SpaceShip', array('name' => 'Alpha'));
        $this->factory->getAsSingleton('SpaceShip');
        
        $self = $this;
        $this->assertThrows(function() use ($self) {
            $self->factory->getAsSingleton('SpaceShip');
        });
    }
    
    //TODO: should it be an error to get() a singleton with overrides?
    
    /**
     * @test
     */
    public function allowsSettingSingletons()
    {
        $this->factory->defineEntity('SpaceShip');
        $ss = new TestEntity\SpaceShip("The mothership");
        
        $this->factory->setSingleton('SpaceShip', $ss);
        
        $this->assertSame($ss, $this->factory->get('SpaceShip'));
    }
    
    /**
     * @test
     */
    public function allowsUnsettingSingletons()
    {
        $this->factory->defineEntity('SpaceShip');
        $ss = new TestEntity\SpaceShip("The mothership");
        
        $this->factory->setSingleton('SpaceShip', $ss);
        $this->factory->unsetSingleton('SpaceShip');
        
        $this->assertNotSame($ss, $this->factory->get('SpaceShip'));
    }
    
    /**
     * @test
     */
    public function allowsOverwritingExistingSingletons()
    {
        $this->factory->defineEntity('SpaceShip');
        $ss1 = new TestEntity\SpaceShip("The mothership");
        $ss2 = new TestEntity\SpaceShip("The battlecruiser");
        
        $this->factory->setSingleton('SpaceShip', $ss1);
        $this->factory->setSingleton('SpaceShip', $ss2);
        
        $this->assertSame($ss2, $this->factory->get('SpaceShip'));
    }
}