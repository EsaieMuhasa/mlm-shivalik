<?php
namespace PHPBackend;

class RouteNotFoundException extends PHPBackendException
{
    /**
     * CODE DU NOT FOUND DU PROTOCILE HTTP
     * @var integer
     */
    const HTTP_NOT_FOUND_CODE = 404;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\PHPBackendException::__construct()
     */
    public function __construct(?string $message, $errorCode = self::HTTP_NOT_FOUND_CODE, $previous = null)
    {
        parent::__construct($message, $errorCode, $previous);
    }

}

