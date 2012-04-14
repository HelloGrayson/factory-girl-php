<?php
namespace FactoryGirl\Doctrine\ORM\Locking;

/**
 * A hack for pessimistic version locks.
 *
 * @see FactoryGirl\Doctrine\ORM\Repository::useWithPessimisticVersionLock
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
