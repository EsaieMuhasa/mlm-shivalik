<?php
namespace PHPBackend\Dao;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface DAOListener
{
    /**
     * methode de callback appeler lors d'une nouveal evenement
     * @param DAOEvent $event
     */
    public function onEvent (DAOEvent $event) : void;
}

