<?php

namespace Library;


/**
 * Enregistrement de la fonction des callback de chargment dynamique des classe
 */
spl_autoload_register(function($className){
    $file = (dirname(__DIR__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php');
    if (file_exists($file)) {
        require_once $file; 
        return true;
    }
    return false;    
});

session_start();

/**
 * 
 * @param DBEntity $a
 * @param DBEntity $b
 * @return number
 */
function entitySort($a, $b) {
    return strcmp($a->findSortValue(), $b->findSortValue());
}


// function error2exception($severity, $message, $filename, $lineno) {
//     $ex = new \ErrorException($message, 0, $severity, $filename, $lineno);
//     throw new LibException('Erreur fatale', LibException::APP_LIB_ERROR_CODE, $ex);
// }

// set_error_handler('Library\error2exception');

