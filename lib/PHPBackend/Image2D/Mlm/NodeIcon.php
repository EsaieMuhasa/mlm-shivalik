<?php
namespace PHPBackend\Image2D\Mlm;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface NodeIcon
{
    const ICON_PNG = 'png';
    const ICON_JPG = 'jpg';
    
    public function getType () : string;
    /**
     * Revoie l'image par defaut
     * @return string
     */
    public function getDefault () : string;
    
    /**
     * Renvoie l'adresse de l'icone moyene
     * @return string
     */
    public function getMd () : string;
    
    /**
     * revoie l'adresse de la petite icone 
     * @return string
     */
    public function getSm () : string;
    
    /**
     * Revoie l'adresse de la plus petite icone
     * @return string
     */
    public function getXs () : string;
    
    /**
     * Revoie l'image par defaut
     * @return string
     */
    public function getAbsoluteDefault () : string;
    
    /**
     * Renvoie l'adresse de l'icone moyene
     * @return string
     */
    public function getAbsoluteMd () : string;
    
    /**
     * revoie l'adresse de la petite icone
     * @return string
     */
    public function getAbsoluteSm () : string;
    
    /**
     * Revoie l'adresse de la plus petite icone
     * @return string
     */
    public function getAbsoluteXs () : string;
}

