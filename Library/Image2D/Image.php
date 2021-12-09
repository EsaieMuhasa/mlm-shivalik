<?php
namespace Library\Image2D;


use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Image
{
    
    private $IMG_TYPES = ['png', 'jpg', 'jpeg'];//LES IMAGE PISENT EN CHARGE
    
    const IMAGE_TYPE_PNG = 'png';
    const IMAGE_TYPE_JPG = 'jpg';
    
    /**
     * Chemain absolut vers l'image
     * @var string
     */
    private $fileName;
    
    /**
     * Le rectagle de l'image
     * @var ImageRect
     */
    private $rectangle;
    
    
    /**
     * @var string
     */
    private $type;

    /**
     * Constructeur d'une image
     * @param string $fileName le nom du fichier (chemain absolut du fichier sur le serveur)
     * @param ImageRect $rectangle le rectangle de l'image (important lors du redimenssionnement de l'image)
     * @throws LibException si l'image n'est pas prise en charge
     */
    public function __construct(string $fileName, ?ImageRect $rectangle = null)
    {
        $this->setFileName($fileName);
        
        if ($rectangle === null) {//Si le rectagle de l'image n'a pas ete definie
            if($this->fileExists()){//si l'image existe physiquement sur le serveur
                
                
                if ($this->getType() === self::IMAGE_TYPE_PNG) {//pour les PNG
                    $image = imagecreatefrompng($fileName);
                }else {//pour les JPEG
                    $image = imagecreatefromjpeg($fileName);
                }
                
                if ($image === false) {
                    throw new LibException('Echec de chargement de la ressource '.$fileName);
                }
                
                $width = imagesx($image);
                $hight = imagesy($image);
                
                $rectangle = new ImageRect($width, $hight);
            }else {
                $rectangle = new ImageRect(1, 1);
            }
        }
        
        $this->rectangle = $rectangle;
    }
    
    
    /**
     * Mitateur du nom du fichier de l'image
     * @param string $fileName
     * @throws LibException
     */
    protected function setFileName(string $fileName) : void{
        $matches = array();
        
        if (preg_match('#^(.+)\.([a-zA-Z]{3,5})#', $fileName, $matches)) {
            $extension = strtolower($matches[2]);
            foreach ($this->IMG_TYPES as $type){
                if ($type == $extension) {
                    $this->setType($type);
                    $this->fileName = $fileName;

                    return;
                }
            }
        }
        
        throw new LibException('Image non prise en charge. L\'image doit etre un PNG soit un JPG.');
    }
    
    /**
     * @return string
     */
    public function getType () : ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFileName() : string
    {
        return $this->fileName;
    }

    /**
     * @param string $type
     */
    protected function setType($type) : void
    {
        if (preg_match('#^jpe?g$#', $type)) {
            $this->type = self::IMAGE_TYPE_JPG;
        }else{            
            $this->type = self::IMAGE_TYPE_PNG;
        }
    }

    /**
     * Le fichier exist-il sur le serveur???
     * @return bool
     */
    public function fileExists() : bool{
        return boolval(file_exists($this->getFileName()));
    }
    
    
    
    /**
     * Recuperation de la taille de l'image
     * @return ImageRect
     */
    public function getSize() : ImageRect{
        return $this->rectangle;
    }
    
    /**
     * Recuperation de la geometrie de l'image.
     * @throws LibException si l'image d'existe pas physiquement sur le serveur
     * @return ImageRect
     */
    public function getReelSize() : ImageRect{
        if (!$this->fileExists()) {
            throw new LibException('Impossible de deduire le rectagle de l\'image car il n\'existe pas physiquement sur le serveur.');
        }

    }
}

