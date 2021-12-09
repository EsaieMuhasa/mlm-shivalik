<?php
namespace Library\Image2D;

/**
 * Utilitaire de conservation du rectagle d'une image
 * @author Esaie MHS
 *        
 */
class ImageRect
{
    /**
     * @var int
     */
    private $width;
    
    /**
     * @var int
     */
    private $height;
    
    /**
     * @var int
     */
    private $x;
    
    /**
     * @var int
     */
    private $y;
    
    
    /**
     * constructeur du rectangle d'une image
     * @param int $width la largeur du rectangle
     * @param int $height la hoteur du rectagngle
     * @param int $x absice
     * @param int $y ordonnee
     */
    public function __construct(int $width, int $height, int $x=0, int $y=0)
    {
        $this->width = $width;
        $this->height = $height;
        $this->x = $x;
        $this->y = $y;
    }
    
    
    
    /**
     * Revoie la taille par defaut d'une image dans une page web
     * @return ImageRect
     */
    public static function getDefaultSize () : ImageRect {
        return new ImageRect(1920, 1080);
    }
    
    /**
     * Pour les ecrans moyens
     * @return ImageRect
     */
    public static function getDefaultMdSize () : ImageRect {
        return new ImageRect(1920, 1080);
    }
    
    /**
     * Revoie la taille des images minuatures
     * @return ImageRect
     */
    public static function getDefaultMinuatureSize () : ImageRect {
        return new ImageRect(576, 324);
    }
    
    /**
     * Renvoie la plus petite taile carre d'une image
     * @return ImageRect
     */
    public static function getDefaultXsCarreSize() : ImageRect{
        return new ImageRect(80, 80);
    }
    
    /**
     * Revoie la petite taille d'un image carre 
     * @return ImageRect
     */
    public static function getDefaultSmCarreSize() : ImageRect{
        return new ImageRect(200, 200);
    }
    
    /**
     * Revoie la taille par defut d'un image
     * @return ImageRect
     */
    public static function getDefaultCarreSize() : ImageRect{
        return new ImageRect(512, 512);
    }
    
    
    //POUR LES PHOTOS DES PROFILS
    public static function getProfilSize() : ImageRect{
        return ImageRect::getDefaultCarreSize();
    }
    
    public static function getProfilSmSize() : ImageRect{
        return ImageRect::getDefaultSmCarreSize();
    }
    
    public static function getProfilXsSize() : ImageRect{
        return ImageRect::getDefaultXsCarreSize();
    }
    
    /**
     * taile portrait en pixel d'un format A6 a une resolution de 300 pixel/pouce
     * @return ImageRect
     */
    public static function getA6Portrait() : ImageRect{
        return new ImageRect(1240, 1748);
    }
    
    /**
     * @return ImageRect
     */
    public static function getA4Portrait() : ImageRect{
        return new ImageRect(2480, 3508);
    }
    
    /**
     * @return ImageRect
     */
    public static function getA3Portrait() : ImageRect{
        return new ImageRect(3508, 4961);
    }
    
    
    /**
     * Taile paysage en pixel d'un format A6 sur une resolution de 300 pixel/pouce
     * @return ImageRect
     */
    public static function getA6Landscape() : ImageRect{
        return new ImageRect(1748, 1240);
    }
    
    /**
     * @return ImageRect
     */
    public static function getA4Landscape() : ImageRect{
        return new ImageRect(3508, 2480);
    }
    
    public static function getA3Landscape() : ImageRect{
        return new ImageRect(4961, 3508);
    }
    
    /**
     * @return number
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return number
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return number
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return number
     */
    public function getY()
    {
        return $this->y;
    }

}

