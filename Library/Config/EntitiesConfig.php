<?php
namespace Library\Config;

use Library\LibException;
use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class EntitiesConfig
{
    
    /**
     * collection des metadonnees des entites
     * @var EntityMetadata[]
     */
    private $metadatas = [];

    /**
     * constructeur d'initialisation
     * @param \DOMDocument $dom
     */
    public function __construct(\DOMDocument $dom)
    {
        $this->metadatas = $this->load($dom);
    }
    
    /**
     * Lecture du fichier de configurations
     * @param \DOMDocument $xml
     * @throws LibException
     * @throws DAOException
     * @return array
     */
    protected function load (\DOMDocument $xml) : array {
        $datas = [];        
        
        $entities = $xml->getElementsByTagName('entities');
        if ($entities->count() == 0) {
            throw new DAOException("Aucune configuration des entites n'a ete definie dans le fichier de configuration");
        }
        
        $elements = $entities[0]->childNodes;
        for ($i = 0; $i < $elements->length; $i++) {
            
            /**
             * @var \DOMNode $element
             */
            $element = $elements->item($i);
            if ($element->nodeName == 'entity') {
                $meta = new EntityMetadata($element->getAttribute('name'), $element->getAttribute('specification'), $element->getAttribute('implementation'), 
                    $element->hasAttribute('alias')? $element->getAttribute('alias') : null);
                $datas[] = $meta;
            }
        }
        
        return $datas;
    }
    
    /**
     * La configuration de cette entity existe??
     * @param string $name
     * @return bool
     */
    public function hasEntity (string $name) : bool{
        
        foreach ($this->metadatas as $metadata) {
            if ($metadata->getName() == $name || $metadata->getSimpleName() == $name) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * y a-t-il un entite ayant pour l'alias la valeur en parametre?
     * @param string $alias
     * @return bool
     */
    public function hasAlias (string $alias) : bool {
        foreach ($this->metadatas as $metadata) {
            if ($metadata->getAlias() == $alias) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Recherche des metadonnees associer a une alias
     * @param string $alias
     * @throws DAOException
     * @return EntityMetadata
     */
    public function findMetadata (string $alias) : EntityMetadata{
        foreach ($this->metadatas as $metadata) {
            if ($metadata->getAlias() == $alias) {
                return $metadata;
            }
        }
        
        throw new DAOException("Aucune metadonnees dans la configuration du DAO pour l'alias {$alias}");
    }
    
    /**
     * Le meta donnees de l'implemetation en parametre
     * @param string $implementation
     * @return EntityMetadata
     */
    public function forImplementation (string $implementation) : EntityMetadata{
        foreach ($this->metadatas as $metadata) {
            if ($metadata->getImplementation() == $implementation) {
                return $metadata;
            }
        }
        
        throw new DAOException("Aucune metadonnees dans la configuration du DAO associer a l'implementation {$implementation}");
    }
    
    /**
     * Recuperation des metadonnees d'un entite
     * @param string $name le nom de la classe de l'entite (simple ou complet avec namespace)
     * @throws DAOException
     * @return EntityMetadata
     */
    public function getMetadata (string $name) : EntityMetadata{
        foreach ($this->metadatas as $metadata) {
            if ($metadata->getName() == $name || $metadata->getSimpleName() == $name) {
                return $metadata;
            }
        }
        
        throw new DAOException("Aucune metadonnée dans la configuration du DAO ne fait reference à '{$name}'");
    }
}

