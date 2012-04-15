<?php

namespace FactoryGirl\Tests\Provider\Doctrine\ORM\TestEntity;

/**
 * @Entity(repositoryClass="FactoryGirl\Tests\Provider\Doctrine\ORM\Repository")
 */
class User
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    public $id;
}
