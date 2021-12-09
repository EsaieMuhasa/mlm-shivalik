<?php
namespace Library\Image2D\Mlm\Ternary;

use Library\LibException;
use Library\Image2D\Image;
use Library\Image2D\ImageRect;
use Library\Image2D\Point;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class TernaryTreeRender
{
    /**
     * @var TernaryTreeBuilder
     */
    private $builder;
    
    /**
     * Constructeur d'initialisation
     * @param TernaryTreeBuilder $builder
     */
    public function __construct(TernaryTreeBuilder $builder) {
        $this->builder = $builder;
    }
    
    /**
     * generer l'image de l'arbre
     * @return string
     */
    public function render (?string $name=null) : string {
        //determination du plan
        $height = $this->builder->getHeight() + ($this->builder->getRange() * 2);
        $width = $this->builder->getWidth() + ($this->builder->getRange() * 2);
        
        $drawables = $this->builder->getDrawables();
        $lines = $this->builder->getLines();
        
        //die("[ ".$this->builder->getLevel());
        if ($this->builder->getLevel() <= 3) {
            $paper = ImageRect::getA6Landscape();
        }else if ($this->builder->getLevel()==4 || $this->builder->getLevel()==5) {
            $paper = ImageRect::getA4Landscape();
        }else {
            $paper = ImageRect::getA3Landscape();
        }
        
        
        
        $plan = @imagecreate($paper->getWidth(), $paper->getHeight());
        if ($plan === false) {
            throw new LibException("Echec");
        }
        $translateX = ($this->builder->getWidth()/2) + $this->builder->getRange();//translation sur l'axe des X
        $translateY = $this->builder->getRange();//translation sur l'axe des Y
        
        /**
         * @var int|false $bkcolor
         */
        $bkcolor = imagecolorallocate($plan, 255, 255, 255);//arriere plan
        $color = imagecolorallocate($plan, 0, 0, 0);
        $txtcolor = imagecolorallocate($plan, 200, 80, 20);
        
        
        foreach ($lines as $line) {//on dessine les lignes
            $x1 = $line->getStart()->getX() + $translateX;
            $y1 = $line->getStart()->getY() + $translateY;
            
            $x2 = $line->getEnd()->getX() + $translateX;
            $y2 = $line->getEnd()->getY() + $translateY;
            
            $poit1 = Point::toEchel($x1, $y1, $width, $height, $paper->getWidth(), $paper->getHeight());
            $poit2 = Point::toEchel($x2, $y2, $width, $height, $paper->getWidth(), $paper->getHeight());
            
            $draw = @imageline($plan, $poit1->getX(), $poit1->getY(), $poit2->getX(), $poit2->getY(), $color);
            if (!$draw) {
                throw new LibException("an error occurred while generating the family tree");
            }
            
        }
        
        
        foreach ($drawables as $drawable) {//on desine les cercles
            $cx = $drawable->getX() + $translateX;
            $cy = $drawable->getY() + $translateY;
            $rayon = $this->builder->getRange()/10;
            
            $rayon = ($rayon / $height) * 100;
            $rayon = intval(($paper->getWidth()/ 100) * $rayon);
            
            $center = Point::toEchel($cx, $cy, $width, $height, $paper->getWidth(), $paper->getHeight());
            
            
            //$draw = imagefilledellipse($plan, $cx, $cy, $rayon, $rayon, $color);
            @imagestring($plan, 2, $center->getX()+2, $center->getY()-($rayon/2)-15,  $drawable->getNode()->getNodeName(), $txtcolor);
            
            //la photo
            $image = new Image($drawable->getNode()->getIcon()->getAbsoluteDefault());
            $size = $image->getSize();
            if ($image->getType() == Image::IMAGE_TYPE_PNG) {
                $source = @imagecreatefrompng($drawable->getNode()->getIcon()->getAbsoluteDefault());
            }else {
                $source = @imagecreatefromjpeg($drawable->getNode()->getIcon()->getAbsoluteDefault());
            }
            
            $destination = @imagecreatetruecolor($rayon, $rayon);
            @imagecopyresampled($destination, $source, 0, 0, 0, 0, $rayon, $rayon, $size->getWidth(), $size->getHeight());
            @imagecopyresampled($plan, $destination, $center->getX()-($rayon/2), $center->getY()-($rayon/2), 0, 0, $rayon, $rayon, $rayon, $rayon);
        }
        
        //le rectagle
        $x1 = 20;
        $y1 = 20;
        $x2 = $paper->getWidth()-20;
        $y2 = $paper->getHeight()-20;
        @imagerectangle($plan, $x1, $y1, $x2, $y2, $color);
        imagestring($plan, 200, 20, $paper->getHeight()-18,  "Designed by Ing. Esaie MUHASA", $color);
        
        if ($name == null) {
            $name = "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/tree".time().".png";
        }else {
            $name = "{$_SERVER["DOCUMENT_ROOT"]}/Web/data/{$name}.png";
        }
        
        @imagepng($plan, $name);
        @imagedestroy($plan);
        return $name;
    }
}

