<?php
namespace PHPBackend\Http;

use PHPBackend\Config\FilterConfig;
use PHPBackend\Filter;
use PHPBackend\Application;
use PHPBackend\Dao\DAOAutoload;
use PHPBackend\Dao\DAOManagerFactory;
/**
 * HTTPFilter component
 * @author Esaie MUHASA
 * 19/11/2021 a 22:01
 */
abstract class HTTPFilter implements Filter
{
    
    use DAOAutoload;//pour le chargement automatique des managers
    
    /**
     * @var FilterConfig
     */
    private $config;
    
    /**
     * @var Application
     */
    private $application;
    
    /**
     * constructeur d'initialisation
     * @param \PHPBackend\Application $application
     * @param FilterConfig $config
     */
    public function __construct(\PHPBackend\Application $application, FilterConfig $config)
    {
        $this->application = $application;
        $this->hydrateInterfaces(DAOManagerFactory::getInstance());
        $this->config = $config;
    }
    
    /**
     * Revoie la configuration du filtre
     * @return FilterConfig
     */
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Filter::getConfig()
     */
    public function getConfig () : FilterConfig {
        return $this->config;
    }

}

