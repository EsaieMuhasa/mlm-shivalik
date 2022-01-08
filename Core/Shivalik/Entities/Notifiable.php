<?php
namespace Core\Shivalik\Entities;

/**
 * Tout Element notifiable doit implementer cette interface
 * @author Esaie MUHASA
 *        
 */
interface Notifiable
{
    /**
     * revoie les donnees
     * @return Object
     */
    public function getData ();
    
    /**
     * Revoie la cle de l'object notifiable
     */
    public function getKey () ;
    
    /**
     * renvoie le surnom de l'object notifiable
     * @return string
     */
    public function getNickname () : string;
}

