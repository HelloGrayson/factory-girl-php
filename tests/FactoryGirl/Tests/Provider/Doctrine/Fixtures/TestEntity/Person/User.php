<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Person;

/**
 * @Entity
 */
class User
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;
}
