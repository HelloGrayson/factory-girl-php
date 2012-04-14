<?php
namespace Xi\Doctrine\Zend\Validate;

use Zend_Validate_Abstract;

/**
 * @category   Xi
 * @package    Doctrine
 * @subpackage Zend
 * @author     Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 */
abstract class AbstractExistsValidator extends Zend_Validate_Abstract
{
    /**
     * Constants used for naming parameters in the query and avoiding parameter
     * name conflicts
     */
    const FIELD_PARAMETER_NAME = 'field';
    const EXCLUDE_PARAMETER_PREFIX = 'exclude_';

    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

    /**
     * Entity class
     *
     * @var string
     */
    protected $_entityClass;
    
    /**
     * Field
     *
     * @var string
     */
    protected $_field;

    /**
     * Exclude
     *
     * @var array
     */
    protected $_exclude;

    /**
     * Doctrine entity manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param string $entityClass entity class name for consumption by Doctrine
     * @param string $field
     * @param array $exclude list of existing key-value-pairs to exclude from matching
     */
    public function __construct($em, $entityClass, $field, array $exclude = array())
    {
        $this->_em          = $em;
        $this->_entityClass = $entityClass;
        $this->_field       = $field;
        $this->_exclude     = $exclude;
    }
    
    /**
     * Create query and return result
     *
     * @param mixed $value
     * @return array
     */
    protected function _query($value)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('x')
           ->from($this->_entityClass, 'x')
           ->where("x.{$this->_field} = :" . self::FIELD_PARAMETER_NAME)
           ->setParameter(self::FIELD_PARAMETER_NAME, $this->_value);

        foreach ($this->_exclude as $key => $value) {
            $parameterName = self::EXCLUDE_PARAMETER_PREFIX . $key;
            $qb->andWhere("x.$key != :" . $parameterName)
               ->setParameter($parameterName, $value);
        }

        return $qb->getQuery()->getResult();
    }
}
