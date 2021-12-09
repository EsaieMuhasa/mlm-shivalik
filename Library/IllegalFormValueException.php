<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
class IllegalFormValueException extends LibException
{
    
    /**
     * {@inheritDoc}
     * @see \Library\LibException::__construct()
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}

