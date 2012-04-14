<?php
namespace Xi\Doctrine\Fixtures\TestEntity;

/**
 * @Entity
 */
class Person
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
     * @ManyToOne(targetEntity="SpaceShip", inversedBy="crew")
     * @JoinColumn(name="spaceShip_id", referencedColumnName="id", nullable=true)
     */
    protected $spaceShip;
    
    
    public function __construct($name, SpaceShip $spaceShip = null)
    {
        $this->name = $name;
        $this->spaceShip = $spaceShip;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSpaceShip()
    {
        return $this->spaceShip;
    }
}
