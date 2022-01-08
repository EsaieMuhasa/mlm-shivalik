<?php
namespace PHPBackend\Config;

use PHPBackend\RouteNotFoundException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class FilterConfig
{
    private $name;
    
    /**
     * collection des URL eccouter par un filtre
     * @var FilterRoute[]
     */
    private $routes = [];

    /**
     * constructeur d'initialisation
     * @param string $name
     * @param array $routes
     */
    public function __construct(string $name, array $routes)
    {
        $this->name = $name;
        $this->routes = $routes;
    }
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return \PHPBackend\Config\FilterRoute[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    
    /**
     * Verification s'il y a aumoin un des patterne du filtre qui match l'URL 
     * @param string $url
     * @return bool|array
     */
    public function match (string $url) {
        foreach ($this->getRoutes() as $route) {
            $match = $route->match($url);
            if ($match) {
                return $match;
            }
        }
        return false;
    }
    
    public function getRoute (string $url) : FilterRoute {
        foreach ($this->getRoutes() as $route) {
            $paramsValues = $route->match($url);
            
            if ($paramsValues !== false)
            {
                if ($route->hasParams())
                {
                    $params = array();
                    $paramsNames = $route->getParamsNames();
                    
                    foreach ($paramsValues as $key => $paramValue)
                    {
                        if ($key!=0)
                        {
                            $params[$paramsNames[$key-1]] = $paramValue;
                        }
                    }
                    $route->setParams($params);
                }
                return $route;
            }
        }
        throw new RouteNotFoundException("Auncute route du filtre {$this->getName()} ne correpond a l'URL {$url}");
    }

}

