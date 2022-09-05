<?php
namespace PHPBackend\Validator;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class IllegalFormValueException extends PHPBackendException
{
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\PHPBackendException::__construct()
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}

