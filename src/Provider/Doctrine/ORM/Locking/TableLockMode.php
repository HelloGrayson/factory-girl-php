<?php
namespace FactoryGirl\Provider\Doctrine\ORM\Locking;

class TableLockMode
{
    public const READ = 8;
    public const WRITE = 16;

    /**
     * @param int $mode
     * @return null|string
     */
    public static function toString($mode)
    {
        switch ($mode) {
            case self::READ: return 'READ';
            case self::WRITE: return 'WRITE';
        }
    }
}
