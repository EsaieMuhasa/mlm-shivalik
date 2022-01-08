<?php
namespace PHPBackend\Image2D;

/**
 * Representation d'un point materiel
 * @author Esaie MUHASA
 *        
 */
class Point
{
    /**
     * @var int
     */
    private $x;
    
    /**
     * @var int
     */
    private $y;
    
    /**
     * @var int
     */
    private $width;

    /**
     * constructeur d'initialisation
     * @param int $x
     * @param int $y
     * @param int $width
     */
    public function __construct(int $x, int $y, int $width = 1)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
    }
    
    /**
     * pour effectuer un transation des cordonnees
     * @param int $x
     * @param int $y
     */
    public function translate (int $x, int $y) : void {
        $this->x = $x;
        $this->y = $y;
    }
    
    /**
     * @return number
     */
    public function getX() : int
    {
        return $this->x;
    }

    /**
     * @return number
     */
    public function getY() : int
    {
        return $this->y;
    }

    /**
     * @return number
     */
    public function getWidth() : int
    {
        return $this->width;
    }
    
    /**
     * translation d'un point d'un echel A dans un echel B
     * @param int $x
     * @param int $y
     * @param int $widhtReel
     * @param int $heightReel
     * @param int $widhtImage
     * @param int $heightImage
     * @return Point
     */
    public static function toEchel (int $x, int $y, int $widhtReel, int $heightReel, int $widhtImage, int $heightImage) : Point {
        $wr_100 = ($x / $widhtReel) * 100;
        $hr_100 = ($y / $heightReel) * 100;
        
        $echelX = ($widhtImage / 100) * $wr_100;
        $echelY = ($heightImage / 100) * $hr_100;
        
        return new Point(intval($echelX), intval($echelY));
    }

}

