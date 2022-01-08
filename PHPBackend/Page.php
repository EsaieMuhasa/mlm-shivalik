<?php
namespace PHPBackend;

/**
 * Specification des comportements d'un page
 * @author Esaie MUHASA
 *        
 */
interface Page extends ApplicationComponent
{
    /**
     * nom de la variable qui confiens la vue rendue lors de son injection dans le template
     * @var string
     */
    const ATT_VIEW = 'PHP_BACKEND_VIEWS_CONTENT';
    
    /**
     * revoie le rendu de la page
     * @return string
     */
    public function getGeneratedPage () : string;
    
    /**
     * revoie le redue de page specifique aux mails
     * @return string
     */
    public function getGeneratedMail() : string;
    
    /**
     * revoie le titre de la page
     * @return string|NULL
     */
    public function getTitle () : ?string;
    
    /**
     * initilisation du titre de la page
     * @param string $title
     */
    public function setTitle (?string $title) : void;
    
    /**
     * initialise la vue qui sera rendue dans la page
     * @param string $viewName
     */
    public function setViewFile (string $viewName): void;
    
}

