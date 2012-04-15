<?php
namespace FactoryGirl\Provider\Doctrine\ORM\Locking;

/**
 * A hack for pessimistic version locks.
 *
 * @see FactoryGirl\Provider\Doctrine\ORM\Repository::useWithPessimisticVersionLock
 */
interface VersionLockable
{
    /**
     * @return mixed
     */
    public function getVersion();

    /**
     * @return void
     */
    public function incrementVersion();
}
