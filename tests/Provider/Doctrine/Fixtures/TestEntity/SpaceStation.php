<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class SpaceStation
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;
    
    /** @Column */
    protected $name = 'Babylon5';
    
    public function __construct($name)
    {
        $this->name = $name;
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
}
