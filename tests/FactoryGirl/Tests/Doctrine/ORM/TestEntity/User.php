<?php

namespace FactoryGirl\Doctrine\ORM\TestEntity;

/**
 * @Entity(repositoryClass="FactoryGirl\Doctrine\ORM\Repository")
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
