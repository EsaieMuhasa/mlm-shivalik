<?php
namespace Library;

/**
 *
 * @author Esaie MHS
 *        
 */
trait DAOAutoload
{
    
    /**
     * Injecteur de dependance des DAOs
     * @param DAOManager $daoManager
     * @return void
     */
    public function autoHydrate(DAOManager $daoManager)
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
                $propertie->setValue($this, $daoManager->findManager($propertieName));
                $propertie->setAccessible(false);
            }
        }
        
    }
}

