<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;

/**
 * @Entity
 */
class Commander
{
    /**
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     * @Column(
     *     name="id",
     *     type="integer"
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @Embedded(
     *     class="FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Name",
     *     columnPrefix=false
     * )
     *
     * @var Name
     */
    private $name;

    public function __construct()
    {
        $this->name = new Name();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }
}
