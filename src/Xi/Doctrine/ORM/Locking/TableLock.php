<?php
namespace Xi\Doctrine\ORM\Locking;

use Xi\Doctrine\ORM\Repository;

/**
 * Implements a transaction that locks the underlying table. Probably only
 * works with MySQL, but is in any case very fragile.
 *
 * @see transaction()
 */
class TableLock
{
    /**
     * @var Repository
     */
    private $repository;
    
    /**
     * @param Repository $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @return Repository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * Attempt to acquire a table level lock in MySQL for the duration of the
     * given transaction. IS NOT IN ANY WAY GUARANTEED TO WORK. MySQL requires
     * that the aliases through which a table is accessed during this
     * transaction are enumerated when locking tables, which due to the nature
     * of Doctrine is a somewhat difficult task. Nevertheless, in simple cases a
     * good guesstimate as to the table aliases can be made; see relevant
     * methods below.
     * 
     * @param int $lockMode a TableLockMode constant
     * @param callback $transaction
     * @return mixed
     * @throws LockException
     */
    public function transaction($lockMode, $transaction)
    {
        $lock = $this->getLockString($lockMode);
        $unlock = $this->getUnlockString();
        
        return $this->getRepository()->transaction(function(EntityManager $em, Repository $repository) use($lock, $unlock, $transaction) {
            $conn = $em->getConnection();
            $conn->executeQuery($lock);
            try {
                $result = $repository->transaction($transaction);
                $conn->executeQuery($unlock);
                return $result;
            } catch (Exception $e) {
                // Transaction rollback does not release table locks
                $conn->executeQuery($unlock);
                throw $e;
            }
        });
    }
    
    /**
     * Get the MySQL statement for locking the table underlying this repository
     * for simple read and/or write operations given an appropriate lock mode
     * 
     * @param int $lockMode a TableLockMode constant
     * @return string
     * @throws LockException
     */
    private function getLockString($lockMode)
    {
        $lockModeString = TableLockMode::toString($lockMode);
        if (!$lockModeString) {
            throw new LockException("Invalid lock mode: $lockMode");
        }
        
        $tableName = $this->getTableName();
        $aliases = $this->getTableAliasGuesstimates($tableName);
        
        return $this->constructLockString($tableName, $aliases, $lockModeString);
    }
    
    /**
     * @return string
     */
    private function getTableName()
    {
        // Blatant violation of law of demeter
        return $this->getRepository()->getClassMetadata()->getTableName();
    }
    
    /**
     * @param string $tableName
     * @param array $aliases
     * @param string $lockModeString
     * @return string
     */
    private function constructLockString($tableName, array $aliases, $lockModeString)
    {
        $lock = "LOCK TABLES $tableName $lockModeString";
        foreach ($aliases as $alias) {
            $lock .= ", $tableName as $alias $lockModeString";
        }
        return $lock;
    }
    
    /**
     * Attempt to guess at the table name aliases used by Doctrine for a given
     * table name
     * 
     * @param string $tableName
     * @return array
     */
    private function getTableAliasGuesstimates($tableName)
    {
        return array_unique(array(
            // the default generated alias: the first letter of the table name prepended with a zero
            strtolower(substr($tableName, 0, 1)) . '0',
            // a generic alias used by Doctrine in many cases
            't0'
        ));
    }
    
    /**
     * The MySQL statement required to unlock tables after a transaction
     * 
     * @return string
     */
    private function getUnlockString()
    {
        return 'UNLOCK TABLES';
    }
}