<?php
namespace PHPBackend;

use PHPBackend\Config\FilterConfig;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface Filter
{
    /**
     * Revoie la configuration du filtre
     * @return FilterConfig
     */
    public function getConfig () : FilterConfig;
    
    /**
     * methode appeler pour effectuer le filtrage
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function doFilter (Request $request, Response $response) : void;
}

