<?php
namespace PHPBackend\Image2D;


use PHPBackend\PHPBackendException;

/**
 * Classe utilitaire de base pour la construction d'une image
 * @author Esaie MHS
 *        
 */
abstract class ImageBuilder
{
    /**
     * L'image source
     * @var Image
     */
    protected $image;
    
    
    /**
     * Constructeur de la classe utilitaire de construction d'une image
     * @param Image $image
     */
    public function __construct(Image $image){
        $this->image = $image;
    }
    
    /**
     * Construction d'une image.
     * @return void
     * @throws PHPBackendException s'il y a erreur lors la creation de l'image
     */
    public abstract function build () : void;
    
}

