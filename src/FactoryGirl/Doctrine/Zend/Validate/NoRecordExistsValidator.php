<?php
namespace Xi\Doctrine\Zend\Validate;

/**
 * @category   Xi
 * @package    Doctrine
 * @subpackage Zend
 * @author     Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 */
class NoRecordExistsValidator extends AbstractExistsValidator
{
    /**
     * Is valid
     *
     * @param  string
     * @return boolean
     */
    public function isValid($value)
    {
        $valid = true;
        $this->_setValue($value);

        $result = $this->_query($value);

        if ($result) {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
