<?php
namespace Xi\Doctrine\ORM;

use Doctrine\DBAL\LockMode,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\OptimisticLockException,
    Xi\Doctrine\ORM\Locking\VersionLockable,
    Xi\Doctrine\ORM\Locking\TableLock,
    Xi\Doctrine\ORM\Locking\LockException,
    Xi\Doctrine\ORM\QueryBuilder;

class Repository extends EntityRepository
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $builder 
     * @return mixed result
     */
    protected function getQueryResult($builder)
    {
        return $builder->getQuery()->getResult();
    }
    
    /**
     * @param \Doctrine\ORM\QueryBuilder $builder 
     * @param callback(Exception) $fallback optional
     * @return object | null result or return value from fallback
     */
    protected function getSingleQueryResult($builder, $fallback = null)
    {
        return $this->attemptQuery(function() use($builder) {
            return $builder->getQuery()->getSingleResult();
        }, $fallback);
    }
    
    /**
     * @param \Doctrine\ORM\QueryBuilder $builder 
     * @param callback(Exception) $fallback optional
     * @return object | null result or return value from fallback
     */
    protected function getSingleScalarQueryResult($builder, $fallback = null)
    {
        return $this->attemptQuery(function() use($builder) {
            return $builder->getQuery()->getSingleScalarResult();
        }, $fallback);
    }
    
    /**
     * Guards against NoResultException and NonUniqueResultException within a
     * callback. Uses a fallback callback in case an exception does occur.
     * 
     * @param callback $do
     * @param callback $fallback optional
     * @return mixed
     */
    private function attemptQuery($do, $fallback = null)
    {
        if (null === $fallback) {
            $fallback = function() {};
        }
        try {
            return $do();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return $fallback($e);
        } catch (\Doctrine\ORM\NonUniqueResultException $e) {
            return $fallback($e);
        }
    }
    
    /**
     * Create a query builder, perform the given operation on it and return the
     * query builder. The operation callback receives the query builder and its
     * associated expression builder as arguments.
     * 
     * @param callback(QueryBuilder, Doctrine\ORM\Query\Expr) $do
     * @return QueryBuilder
     */
    protected function withQueryBuilder($do)
    {
        $qb = $this->getBaseQueryBuilder();
        $do($qb, $qb->expr());
        return $qb;
    }
    
    /**
     * Create a query builder. Override this in a child class to create a
     * builder of the appropriate type.
     * 
     * @return QueryBuilder
     */
    protected function getBaseQueryBuilder()
    {
        return QueryBuilder::create($this->_em);
    }
    
    /**
     * Gets a reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * @param mixed $identifier The entity identifier.
     * @return object The entity reference.
     */
    public function getReference($identifier)
    {
        return $this->getEntityManager()
                    ->getReference($this->getEntityName(), $identifier);
    }

    /**
     * Perform a callback function within a transaction. If an exception occurs
     * within the function, it's catched, the transaction is rolled back and
     * the exception rethrown.
     *
     * @param callback(Doctrine\ORM\EntityManager, Repository) $transaction
     * @return mixed the callback return value
     * @throws Exception
     */
    public function transaction($transaction)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $conn->beginTransaction();
        try {
            $result = $transaction($em, $this);
            $em->flush();
            $conn->commit();
            return $result;
        } catch (Exception $e) {
            $em->close();
            $conn->rollback();
            throw $e;
        }
    }

    /**
     * Acquires a lock to an entity, provides the entity to a callback function
     * and relinquishes the lock by flushing the entity manager immediately
     * after.
     *
     * @param int $id
     * @param int $lockMode
     * @param callback($entity, Doctrine\ORM\EntityManager, Repository) $callback
     * @return mixed callback return type
     * @throws LockException
     */
    public function useWithLock($id, $lockMode, $callback)
    {
        $entityName = $this->getEntityName();
        return $this->transaction(function($em, $self) use($id, $lockMode, $callback, $entityName) {
            $entity = $self->find($id, $lockMode);
            if (empty($entity)) {
                $message = \sprintf("Could not lock %s entity by id %d: entity not found", $entityName, $id);
                throw new LockException($message);
            }
            $result = $callback($entity, $em, $self);
            return $result;
        });
    }

    /**
     * Calls useWithLock() with a pessimistic write lock mode
     *
     * @param int $id
     * @param callback($entity, Doctrine\ORM\EntityManager, Repository) $callback
     * @return mixed callback return type
     */
    public function useWithPessimisticWriteLock($id, $callback)
    {
        return $this->useWithLock($id, LockMode::PESSIMISTIC_WRITE, $callback);
    }

    /**
     * Acquires an optimistic lock within a pessimistic lock transaction. For
     * use in fail-fast scenarios; guaranteed to throw an exception on
     * concurrent modification attempts. The one to first acquire the write lock
     * will update the version field, leading subsequent acquisitions of the
     * optimistic lock to fail.
     *
     * FIXME: Only works on entities implementing VersionLockable and does not
     * work in conjunction with the Doctrine @Version column.
     *
     * @param int $id
     * @param mixed $lockVersion
     * @param callback($entity, Doctrine\ORM\EntityManager, Repository) $callback
     * @return mixed callback return type
     * @throws OptimisticLockException
     */
    public function useWithPessimisticVersionLock($id, $lockVersion, $callback)
    {
        return $this->useWithPessimisticWriteLock($id, function(VersionLockable $entity, EntityManager $em, $self) use($lockVersion, $callback) {
            if ($entity->getVersion() !== $lockVersion) {
                // FIXME: This isn't the appropriate exception type.
                throw OptimisticLockException::lockFailedVersionMissmatch($entity, $lockVersion, $entity->getVersion());
            }
            $entity->incrementVersion();
            return $callback($entity, $em, $self);
        });
    }
    
    /**
     * @return string
     */
    public function getEntityName()
    {
        return parent::getEntityName();
    }
    
    /**
     * Attempt to acquire a table level lock in MySQL for the duration of the
     * given transaction. IS NOT IN ANY WAY GUARANTEED TO WORK.
     * 
     * @see TableLock
     * @param int $lockMode a TableLockMode constant
     * @param callback $transaction
     * @return mixed
     * @throws LockException
     */
    public function transactionWithTableLock($lockMode, $transaction)
    {
        return $this->getTableLock()->transaction($lockMode, $transaction);
    }
    
    /**
     * @return TableLock
     */
    private function getTableLock()
    {
        return new TableLock($this);
    }
}