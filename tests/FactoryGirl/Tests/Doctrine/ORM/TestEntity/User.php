<?php

namespace Xi\Doctrine\ORM\TestEntity;

/**
 * @Entity(repositoryClass="Xi\Doctrine\ORM\Repository")
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
