<?php
namespace PHPBackend;

/**
 * specification d'une application
 * @author Esaie MUHASA
 *        
 */
interface Application
{
    /**
     * Renveoie la configuration de l'application
     * @return AppConfig
     */
    public function getConfig () : AppConfig;
    
    /**
     * Revoie le controller concerner, pour la requette encours
     * @return Controller
     */
    public function getController () : Controller;
    
    /**
     * revoie le nom de l'application
     * @return string
     */
    public function getName() : string;
    
    /**
     * revoie le nom du dossier conteneur des applications
     * @return string|NULL
     */
    public function getContainer() : ?string;
    /**
     * revoie la l'element qui incapsule la requette
     * @return Request
     */
    public function getRequest () : Request;
    
    /**
     * revoie l'incapulateur de la reposne
     * @return Response
     */
    public function getResponse () : Response;
    
    /**
     * pour journaliser une exception
     * @param PHPBackendException $exception
     */
    public function logger (PHPBackendException $exception): void;
    
    /**
     * execution de l'application
     */
    public function run () : void;
}

