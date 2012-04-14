<?php

namespace FactoryGirl\Tests\Doctrine\ORM\TestEntity;

/**
 * @Entity(repositoryClass="FactoryGirl\Tests\Doctrine\ORM\Repository")
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
