<?php
namespace PHPBackend\Image2D;


use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class ImagePasting extends ImageBuilder
{
    
    /**
     * Collection des images a coller ensemble
     * @var Image[]
     */
    protected $images;
    
    /**
     * {@inheritDoc}
     * @see ImageBuilder::__construct()
     * @param Image[] collection des images a coller
     * @param int $policy les trategie de collage des images
     * @throws PHPBackendException si la collection des images est vide, soit si l'une des images a coller n'existe pas physiquement sur le disque dur
     */
    public function __construct(Image $image, array $images, int $policy = 0)
    {
        parent::__construct($image);
        if ($images === null || empty($images)) {
            throw new PHPBackendException('La collection des images qui serons coller ne doit pas etre vide');
        }
        
        /**
         * @var Image $img
         */
        foreach ($images as $img){
            if (!$img->fileExists()) {
                throw new PHPBackendException("Image '{$img->getFileName()}' n'existe pas sur le disque dur");
            }
        }
        
        $this->images = $images;
    }

    /**
     * {@inheritDoc}
     * @see ImageBuilder::build()
     */
    public function build (): void
    {
        // TODO Auto-generated method stub
        
    }



}

