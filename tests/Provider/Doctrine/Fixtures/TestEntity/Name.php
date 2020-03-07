<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;

/**
 * @Embeddable
 */
final class Name
{
    /**
     * @Column(
     *     name="first_name",
     *     type="string",
     *     length=100,
     *     nullable=true
     * )
     *
     * @var string|null
     */
    private $first;

    /**
     * @Column(
     *     name="last_name",
     *     type="string",
     *     length=100,
     *     nullable=true
     * )
     *
     * @var string|null
     */
    private $last;

    public function first(): ?string
    {
        return $this->first;
    }

    public function last(): ?string
    {
        return $this->last;
    }
}
