<?php
namespace Library;

/**
 * 
 * @author Esaie MHS
 *
 */
abstract class AbstractCrypteur
{
    
    use StringCrypteur;
    
    /**
     * Serialisation d'un object au format XML
     * @return string
     */
    public abstract function toXML();
    
    
    /**
     * Serialisation d'un object au format JSON
     * @return string
     */
    public abstract function toJSON();
    
    /**
     * Recuperation des donnees JSON encrypter
     * @return string|NULL
     */
    public function toEncryptedJSON() : ?string{
        return $this->encryption($this->toJSON());
    }
    
    /**
     * Recuperation des donnees au format XML encrypter
     * @return string|NULL
     */
    public function toEncryptedXML() : ?string{
        return $this->encryption($this->toXML());
    }
}

