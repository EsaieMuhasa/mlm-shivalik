<?php
namespace Library\Config;

use Library\DAOException;

/**
 *
 * @author Esaie MHS
 *        
 */
class EntityMetadata
{
    
    const RGX_SIMPLE_CLASS_NAME = '#^.+\\\\([A-Za-z0-9]+)$#';
    
    /**
     * le nom complet de la classe de l'entity
     * @var string
     */
    private $name;
    
    
    /**
     * le nom de la classe complet du specification de l'entity
     * @var string
     */
    private $specification;
    
    /**
     * le nom de la variable.
     * utile le noms des varibles sont diferent de celle de la specification
     * @var string
     */
    private $alias;
    
    /**
     * le nom de la classe qui contiens l'implementation
     * @var string
     */
    private $implementation;

    /**
     * constructeur d'initialisation des metadonnee d'un entity
     * @param string $name
     * @param string $specification
     * @param string $implementation
     * @param string $alias
     */
    public function __construct(string $name, string $specification, string $implementation, ?string $alias=null)
    {
        $this->name = $name;
        $this->specification = $specification;
        $this->implementation = $implementation;
        $this->alias = $alias;
    }
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Revoie le nom simple de l'entitee
     * @return string
     */
    public function getSimpleName () : string {
        $matches = array();
        if (preg_match(self::RGX_SIMPLE_CLASS_NAME, $this->name, $matches)) {
            return $matches[1];
        }
        
        throw new DAOException("Une erreur est survenue lors de la recuperation du nom simple de la class '{$this->name}'");
    }

    /**
     * @return string
     */
    public function getSpecification() : string
    {
        return $this->specification;
    }
    
    /**
     * @return string
     */
    public function getImplementation() : string
    {
        return $this->implementation;
    }
    
    /**
     * @return string
     */
    public function getAlias() : string
    {
        if ($this->alias == null) {
            $matches = array();
            if (preg_match(self::RGX_SIMPLE_CLASS_NAME, $this->specification, $matches)) {
                return lcfirst($matches[1]);
            }
            throw new DAOException("l'alias de '{$this->name}' est invalide");
        }
        return $this->alias;
    }

}

