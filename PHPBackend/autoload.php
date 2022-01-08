<?php

namespace PHPBackend;


/**
 * Enregistrement de la fonction des callback de chargment dynamique des classe
 */
spl_autoload_register( function($className) {
    $file = (dirname(__DIR__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php');
    if (file_exists($file)) {
        require_once $file; 
        return true;
    }
    return false;    
});

// $hadler = new HTTPSessionHandler();
// session_set_save_handler($hadler, true);

// HTTPSessionHandler::getSessions();
// exit();
// session_start();



// function error2exception($severity, $message, $filename, $lineno) {
//     $ex = new \ErrorException($message, 0, $severity, $filename, $lineno);
//     throw new LibException('Erreur fatale', LibException::APP_LIB_ERROR_CODE, $ex);
// }

// set_error_handler('PHPBackend\error2exception');

