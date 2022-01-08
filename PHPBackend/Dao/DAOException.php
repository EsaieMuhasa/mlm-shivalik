<?php
namespace PHPBackend;


/**
 *
 * @author Esaie MHS
 *        
 */
class DAOException extends PHPBackendException
{

    const ERROR_CODE = 505;
    /**
     *
     * @param mixed $message
     *            le message d'erreur
     * @param mixed $code
     *            [optional] le code d'erreur
     * @param mixed $previous
     *            [optional] l'exception precedante
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

