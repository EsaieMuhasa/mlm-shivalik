<?php
namespace PHPBackend\Config;

use PHPBackend\AbstractRoute;

/**
 * route specifique aux Filtres
 * @author Esaie MUHASA
 * le 19/11/2021 a 21h:51
 *        
 */
class FilterRoute extends AbstractRoute
{
    
    /**
     * la priorite d'execution d'un filtre
     * doit etre une valeur numerique superieur ou egale a zero
     * @var int
     */
    private $priority;

    /**
     * {@inheritDoc}
     * @see \PHPBackend\AbstractRoute::__construct()
     */
    public function __construct(string $urlPattern, int $priority = 0, $paramsNames = array())
    {
        parent::__construct($urlPattern, $paramsNames);
        $this->priority = $priority;
    }
    
    
    /**
     * @return number
     */
    public function getPriority() : int
    {
        return $this->priority;
    }
    
}

