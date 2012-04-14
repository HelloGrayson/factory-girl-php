<?php

namespace FactoryGirl\Doctrine\Fixtures\TestAnotherEntity;

/**
 * @Entity
 */
class Artist
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    private $id;
}
