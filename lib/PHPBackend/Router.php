<?php
namespace PHPBackend;

/**
 *
 * @author Esaie MHS
 *        
 */
class Router
{

    /**
     * La collection des routes
     * @var Route[]
     */
    private $routes;
    
    /**
     * Constructeur d'initialisation
     */
    public function __construct()
    {
        $this->routes = array();
    }
    
    /**
     * Ajout d'une route a la collection des routes du router
     * @param Route $route
     * @return void
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }
    
    /**
     * Recupration de la route correspondant a l'URL
     * @param string $url l'url issue de la requette du client
     * @throws RouteNotFoundException si aucune route ne correspond a l'url
     * @return \PHPBackend\Route la route correspondant a l'url de la requette du client
     */
    public function getRoute($url)
    {
        foreach ($this->routes as $route)
        {
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
        throw new RouteNotFoundException("Désolez! Aucune réssource ne correspond à l'URL \"".$url."\"");
    }
}

