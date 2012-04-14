<?php

namespace FactoryGirl\Tests\Doctrine\Fixtures\TestEntity\Person;

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
