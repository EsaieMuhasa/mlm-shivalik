<?php
namespace PHPBackend;

/**
 * specification d'une reponse
 * @author Esaie MUHASA
 *        
 */
interface Response extends ApplicationComponent
{
    const ERROR_NOT_FOUND = 404;
    
    /**
     * ajout un element dans l'antete
     * @param string $header
     */
    public function addHeader (string $header) : void;
    
    /**
     * evoie de la reponse au client
     */
    public function send () : void;
    
    /**
     * Evoie une repose avec un message d'erreur
     * @param string $message
     * @param int $code
     */
    public function sendError (?string $message=null, int $code=self::ERROR_NOT_FOUND) : void;
    
    /**
     * revoie une exception sous forme 'un message d'erreur
     * @param PHPBackendException $e
     */
    public function sendException (PHPBackendException $e) : void;
    
    /**
     * Effectuee un demande redirection
     * @param string $url
     * aprese execution de cette methode la requette est directement coupee
     */
    public function sendRedirect (string $url) : void;
    
    /**
     * demande envoie de d'un email
     * @param array $destinataires
     * @param string $subject
     * @param string $viewFile
     * @param Controller $controller
     * @return bool
     */
    public function sendMail (array $destinataires, string $subject, string $viewFile, ?Controller $controller = null) : bool;
    
    /**
     * initialise la page a rendre dans la reponse
     * @param Page $page
     */
    public function setPage (Page $page) : void;
}

