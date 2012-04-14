<?php
namespace Xi\Doctrine\ORM\Locking;

/**
 * A hack for pessimistic version locks.
 *
 * @see Xi\Doctrine\ORM\Repository::useWithPessimisticVersionLock
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
