<?php
namespace PHPBackend\Dao;

/**
 * 
 * @author Esaie MUHASA
 *
 */
trait DAOAutoload
{
    
    /**
     * Injecteur des implementations des DAOs
     * @param DAOManagerFactory $daoManager
     * @return void
     */
    public function hydrateInterfaces(DAOManagerFactory $daoManager) : void
    {
        $classe = new \ReflectionClass($this);
        
        $config = $daoManager->getEntitiesConfig();
        
        /**
         * @var \ReflectionProperty[] $properties
         **/
        $properties = $classe->getProperties();
        foreach ($properties as $propertie){
            
            $propertieName=$propertie->getName();
            
            if ($config->hasAlias($propertieName)) {
                $propertie->setAccessible(true);
                $propertie->setValue ($this, $daoManager->find($propertieName));
                $propertie->setAccessible (false);
            }
        }
        
    }
}

