<?php
namespace Xi\Doctrine\Fixtures\TestEntity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class SpaceShip
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;
    
    /** @Column */
    protected $name;
    
    /**
     * @OneToMany(targetEntity="Person", mappedBy="spaceShip")
     */
    protected $crew;
    
    /**
     * @var boolean
     */
    protected $constructorWasCalled = false;
    
    
    public function __construct($name)
    {
        $this->name = $name;
        $this->crew = new ArrayCollection();
        $this->constructorWasCalled = true;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getCrew()
    {
        return $this->crew;
    }
    
    public function constructorWasCalled()
    {
        return $this->constructorWasCalled;
    }
}
