<?php
namespace PHPBackend;

/**
 * specification d'un controleur
 * @author Esaie MUHASA
 *        
 */
interface Controller extends ApplicationComponent
{
    const ATT_VIEW_TITLE = 'PHP_BACKEND_TITLE_VIEW';
    
    /**
     * execute le controlleur 
     */
    public function execute() : void;
    
    /**
     * revoie le nom de l'action executer par le controleur
     * @return string
     */
    public function getAction() : string;
    
    /**
     * revoie le module dans lequel le controleur c trouve
     * @return string
     */
    public function getModule () : string;
    
    /**
     * revoie la vue concerner par le controleur
     * @return string
     */
    public function getView () : string;
    
    /**
     * revoie la page specifique a la vue
     * @return \PHPBackend\Page
     */
    public function getPage () : Page;
}

