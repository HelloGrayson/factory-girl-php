<?php
namespace Xi\Doctrine\Fixtures\TestEntity;

/**
 * @Entity
 */
class Badge
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;
    
    /** @Column */
    protected $label;

    /**
     * @ManyToOne(targetEntity="Person")
     * @JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    protected $owner;
    
    
    public function __construct($label, Person $owner)
    {
        $this->label = $label;
        $this->owner = $owner;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getOwner()
    {
        return $this->owner;
    }

}
