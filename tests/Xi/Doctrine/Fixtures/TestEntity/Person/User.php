<?php

namespace Xi\Doctrine\Fixtures\TestEntity\Person;

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
