<?php
namespace Library\Graphics\ChartJS;

use Library\Serialisation\JSONSerialize;

/**
 * un point materiel dans un chart de la lib chartjs
 * @author Esaie MHS
 *        
 */
class ChartPoint
{
    
    /**
     * Le label sur l'absice
     * @var string
     */
    private $x;
    
    /**
     * le label sur l'axe des ordonnees
     * @var int
     */
    private $y;
    
    use JSONSerialize;

    /**
     * constructeur d'initialisation
     * @param string $x
     * @param int $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    /**
     * @return string
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

