<?php

namespace Xi\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\ConversionException;

/**
 * Type that maps a PHP array to a clob SQL type.
 *
 * @since 2.0
 */
class StatusArrayType extends Type
{
    const STATUSARRAY = 'statusarray';
    
    /**
     * @var string Validation regex
     */
    protected $acceptedPattern = '#^[a-zA-Z0-9:_\.]+$#';
        
    public function getSQLDeclaration(array $fieldDeclaration, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        
        if (!is_array($value)) {
            throw new ConversionException('Value must be an array');
        }

        foreach ($value as $val) {
            if (!preg_match($this->acceptedPattern, $val)) {
                throw new ConversionException("'{$val}' does not match pattern '{$this->acceptedPattern}'");
            }
        }
        
        array_walk($value, function(&$walker) { 
           $walker = '[' . $walker . ']';
        });
        
        return implode(';', $value);
        
        
    }

    public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        
        if ($value === null) {
            return null;
        }

        
        $ret = explode(';', $value);
        
        array_walk($ret, function(&$unwashed) {
           $unwashed = trim($unwashed, '[]');
        });
        
        
        return $ret;
        
    
    }

    public function getName()
    {
        return self::STATUSARRAY;
    }
    
}