<?php
namespace PHPBackend\Image2D;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class DrawableLine
{
    /**
     * @var Point
     */
    private $start;
    
    /**
     * @var Point
     */
    private $end;

    /**
     * cosntructeur d'initialisation
     * @param Point $start
     * @param Point $end
     */
    public function __construct(Point $start, Point $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
    

    /**
     * @return \PHPBackend\Image2D\Point
     */
    public function getStart() : Point
    {
        return $this->start;
    }

    /**
     * @return \PHPBackend\Image2D\Point
     */
    public function getEnd() : Point
    {
        return $this->end;
    }

    /**
     * @param \PHPBackend\Image2D\Point $start
     */
    public function setStart(Point $start) : void
    {
        $this->start = $start;
    }

    /**
     * @param \PHPBackend\Image2D\Point $end
     */
    public function setEnd(Point $end) : void
    {
        $this->end = $end;
    }
}

