<?php
namespace Xi\Doctrine\Fixtures;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection,
    Exception;

/**
 * Creates Doctrine entities for use in tests.
 * 
 * See the README file for a tutorial.
 */
class FixtureFactory
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @var string
     */
    protected $entityNamespace;
    
    /**
     * @var array<EntityDef>
     */
    protected $entityDefs;
    
    /**
     * @var array
     */
    protected $singletons;
    
    /**
     * @var boolean
     */
    protected $persist;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        
        $this->entityNamespace = '';
        
        $this->entityDefs = array();
        
        $this->singletons = array();
        
        $this->persist = false;
    }
    
    /**
     * Sets the namespace to be prefixed to all entity names passed to this class.
     */
    public function setEntityNamespace($namespace)
    {
        $this->entityNamespace = trim($namespace, '\\');
    }
    
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }
    
    
    /**
     * Get an entity and its dependencies.
     * 
     * Whether the entity is new or not depends on whether you've created
     * a singleton with the entity name. See `getAsSingleton()`.
     * 
     * If you've called `persistOnGet()` then the entity is also persisted.
     */
    public function get($name, array $fieldOverrides = array())
    {
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }
        
        $def = $this->entityDefs[$name];
        $config = $def->getConfig();
        
        $this->checkFieldOverrides($def, $fieldOverrides);
        
        $ent = $def->getEntityMetadata()->newInstance();
        $fieldValues = array();
        foreach ($def->getFieldDefs() as $fieldName => $fieldDef) {
            $fieldValues[$fieldName] = array_key_exists($fieldName, $fieldOverrides)
                ? $fieldOverrides[$fieldName]
                : $fieldDef($this);
        }
        
        foreach ($fieldValues as $fieldName => $fieldValue) {
            $this->setField($ent, $def, $fieldName, $fieldValue);
        }
        
        if (isset($config['afterCreate'])) {
            $config['afterCreate']($ent, $fieldValues);
        }
        
        
        if ($this->persist) {
            $this->em->persist($ent);
        }
        
        return $ent;
    }
    
    protected function checkFieldOverrides(EntityDef $def, array $fieldOverrides)
    {
        $extraFields = array_diff(array_keys($fieldOverrides), array_keys($def->getFieldDefs()));
        if (!empty($extraFields)) {
            throw new Exception("Field(s) not in " . $def->getEntityType() . ": '" . implode("', '", $extraFields) . "'");
        }
    }
    
    protected function setField($ent, EntityDef $def, $fieldName, $fieldValue)
    {
        $metadata = $def->getEntityMetadata();
        
        if ($metadata->isCollectionValuedAssociation($fieldName)) {
            $metadata->setFieldValue($ent, $fieldName, new ArrayCollection());
        } else {
            $metadata->setFieldValue($ent, $fieldName, $fieldValue);

            if (is_object($fieldValue) && $metadata->isSingleValuedAssociation($fieldName)) {
                $this->updateCollectionSideOfAssocation($ent, $metadata, $fieldName, $fieldValue);
            }
        }
    }
    
    /**
     * Sets whether `get()` should automatically persist the entity it creates.
     * By default it does not. In any case, you still need to call
     * flush() yourself.
     */
    public function persistOnGet($enabled = true)
    {
        $this->persist = $enabled;
    }
    
    /**
     * A shorthand combining `get()` and `setSingleton()`.
     * 
     * It's illegal to call this if `$name` already has a singleton.
     */
    public function getAsSingleton($name, array $fieldOverrides = array())
    {
        if (isset($this->singletons[$name])) {
            throw new Exception("Already a singleton: $name");
        }
        $this->singletons[$name] = $this->get($name, $fieldOverrides);
        return $this->singletons[$name];
    }
    
    /**
     * Sets `$entity` to be the singleton for `$name`.
     * 
     * This causes `get($name)` to return `$entity`.
     */
    public function setSingleton($name, $entity)
    {
        $this->singletons[$name] = $entity;
    }
    
    /**
     * Unsets the singleton for `$name`.
     * 
     * This causes `get($name)` to return new entities again.
     */
    public function unsetSingleton($name)
    {
        unset($this->singletons[$name]);
    }
    
    /**
     * Defines how to create a default entity of type `$name`.
     * 
     * See the readme for a tutorial.
     * 
     * @return FixtureFactory
     */
    public function defineEntity($name, array $fieldDefs = array(), array $config = array())
    {
        if (isset($this->entityDefs[$name])) {
            throw new Exception("Entity '$name' already defined in fixture factory");
        }
        
        $type = $this->addNamespace($name);
        if (!class_exists($type, true)) {
            throw new Exception("Not a class: $type");
        }
        
        $metadata = $this->em->getClassMetadata($type);
        if (!isset($metadata)) {
            throw new Exception("Unknown entity type: $type");
        }
        
        $this->entityDefs[$name] = new EntityDef($this->em, $name, $type, $fieldDefs, $config);
        
        return $this;
    }
    
    /**
     * @param  string $name
     * @return string
     */
    protected function addNamespace($name)
    {
        $name = rtrim($name, '\\');

        if ($name[0] === '\\') {
            return $name;
        }

        return $this->entityNamespace . '\\' . $name;
    }
    
    protected function updateCollectionSideOfAssocation($entityBeingCreated, $metadata, $fieldName, $value) {
        $assoc = $metadata->getAssociationMapping($fieldName);
        $inverse = $assoc['inversedBy'];
        if ($inverse) {
            $valueMetadata = $this->em->getClassMetadata(get_class($value));
            $collection = $valueMetadata->getFieldValue($value, $inverse);
            if ($collection instanceof Collection) {
                $collection->add($entityBeingCreated);
            }
        }
    }
}
