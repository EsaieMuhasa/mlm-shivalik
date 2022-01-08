<?php
namespace PHPBackend\Image2D\Mlm\Ternary;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class DrawableTernaryNode
{
    /**
     * @var TernaryNode
     */
    private $node;
    
    /**
     * @var DrawableTernaryNode
     */
    private $parent;
    
    /**
     * le niveau auquel est situee un elenment
     * @var int
     */
    private $level;
    
    /**
     * @var int
     */
    private $x;
    
    /**
     * @var int
     */
    private $y;
    
    /**
     * constructeur d'initialisation
     * @param TernaryNode $node
     * @param int $x
     * @param int $y
     * @param int $level
     * @param DrawableTernaryNode $parent
     */
    public function __construct($node, int $x, int $y, int $level = 0, ?DrawableTernaryNode $parent = null) {
        $this->node = $node;
        $this->x = $x;
        $this->y = $y;
        $this->level = $level;
        $this->parent = $parent;
    }
    
    /**
     * @return \PHPBackend\Image2D\Mlm\Ternary\TernaryNode
     */
    public function getNode()
    {
        return $this->node;
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
     * @return \PHPBackend\Image2D\Mlm\Ternary\DrawableTernaryNode
     */
    public function getParent () : ?DrawableTernaryNode
    {
        return $this->parent;
    }

    
}

