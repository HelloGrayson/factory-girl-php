<?php
namespace Xi\Doctrine\Fixtures;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping\ClassMetadata,
    Exception;

/**
 * An internal class that `FixtureFactory` uses to normalize and store entity definitions in.
 */
class EntityDef
{
    private $name;
    
    private $entityType;
    
    /**
     * @var ClassMetadata
     */
    private $metadata;
    
    private $fieldDefs;
    
    private $config;
    
    public function __construct(EntityManager $em, $name, $type, array $fieldDefs, array $config)
    {
        $this->name = $name;
        $this->entityType = $type;
        $this->metadata = $em->getClassMetadata($type);
        $this->fieldDefs = array();
        $this->config = $config;
        
        $this->readFieldDefs($fieldDefs);
        $this->defaultDefsFromMetadata();
    }
    
    private function readFieldDefs(array $params)
    {
        foreach ($params as $key => $def) {
            if ($this->metadata->hasField($key) ||
                    $this->metadata->hasAssociation($key)) {
                $this->fieldDefs[$key] = $this->normalizeFieldDef($def);
            } else {
                throw new Exception('No such field in ' . $this->entityType . ': ' . $key);
            }
        }
    }
    
    private function defaultDefsFromMetadata() {
        $allFields = array_merge($this->metadata->getFieldNames(), $this->metadata->getAssociationNames());
        foreach ($allFields as $fieldName) {
            if (!isset($this->fieldDefs[$fieldName])) {
                $this->fieldDefs[$fieldName] = function() { return null; };
            }
        }
    }
    
    /**
     * Returns the name of the entity definition.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the fully qualified name of the entity class.
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
    
    /**
     * Returns the fielde definition callbacks.
     */
    public function getFieldDefs()
    {
        return $this->fieldDefs;
    }
    
    /**
     * Returns the Doctrine metadata for the entity to be created.
     * @return ClassMetadata
     */
    public function getEntityMetadata()
    {
        return $this->metadata;
    }
    
    /**
     * Returns the extra configuration array of the entity definition.
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    private function normalizeFieldDef($def)
    {
        if (is_callable($def)) {
            return $this->ensureInvokable($def);
        } else {
            return function() use ($def) { return $def; };
        }
    }
    
    private function ensureInvokable($f)
    {
        if (method_exists($f, '__invoke')) {
            return $f;
        } else {
            return function() use ($f) {
                return call_user_func_array($f, func_get_args());
            };
        }
    }
}
