<?php
namespace PHPBackend;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface ApplicationComponent
{
    
    /**
     * doit renvoyer une reference vers l'application qui ait demare le composent
     * @return Application
     */
    public function getApplication () : Application;
}

