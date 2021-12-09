<?php
namespace Library;

use Library\Config\FilterConfig;

/**
 * Filter component
 * @author Esaie MUHASA
 * 19/11/2021 a 22:01
 */
abstract class Filter extends ApplicationComponent
{
    
    use DAOAutoload;//pour le chargement automatique des managers
    
    /**
     * @var FilterConfig
     */
    private $config;
    
    /**
     * {@inheritDoc}
     * @see \Library\ApplicationComponent::__construct()
     */
    public function __construct(\Library\Application $application, $config)
    {
        parent::__construct($application);
        $this->autoHydrate(DAOManager::getInstance());
        $this->config = $config;
    }
    
    /**
     * Revoie la configuration du filtre
     * @return FilterConfig
     */
    public function getConfig () : FilterConfig {
        return $this->config;
    }
    
    /**
     * methode utilitaire de filtrage
     * cette methode est appeler chaque foie que l'URL coincide au pattern du Filter
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public abstract function doFilter (HTTPRequest $request, HTTPResponse $response);

}

