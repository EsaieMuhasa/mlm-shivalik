<?php
namespace Library;

class RouteNotFoundException extends LibException
{
    /**
     * CODE DU NOT FOUND DU PROTOCILE HTTP
     * @var integer
     */
    const HTTP_NOT_FOUND_CODE = 404;
    
    /**
     * {@inheritDoc}
     * @see \Library\LibException::__construct()
     */
    public function __construct(?string $message, $errorCode = self::HTTP_NOT_FOUND_CODE, $previous = null)
    {
        parent::__construct($message, $errorCode, $previous);
    }

}

