<?php
namespace Library\Image2D\Mlm\Ternary;

use Library\Image2D\Mlm\TreeBuilder;
use Library\Image2D\DrawableLine;
use Library\Image2D\Point;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class TernaryTreeBuilder extends TreeBuilder
{
    /**
     * @var int
     */
    private $level;
    
    /**
     * @var int
     */
    private $range;
    
    /**
     * @var DrawableTernaryNode[]
     */
    private $drawables = [];
    
    /**
     * @var DrawableLine[]
     */
    private $lines = [];
    
    /**
     * @var int
     */
    private $height;
    
    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\TreeBuilder::__construct()
     * @param int $range la plus petite distance entre les anfant
     */
    public function __construct($root, int $range)
    {
        parent::__construct($root);
        $this->range = $range;
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\TreeBuilder::getRoot()
     * @return TernaryNode
     */
    public function getRoot()
    {
        return parent::getRoot();
    }

    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\TreeBuilder::getLevel()
     */
    public function getLevel(): int
    {
        if ($this->level == null) {
            
            $levels = array();//doit contenir au plus trois niveaux
            
            $levels[] = $this->countLeftLevel($this->getRoot());
            $levels[] = $this->countMiddleLevel($this->getRoot());
            $levels[] = $this->countRightLevel($this->getRoot());
            
            $max = 0;
            foreach ($levels as $level) {
                if ($level > $max) {
                    $max = $level;
                }
            }
            
            $this->level = $max;
            
        }
        return $this->level;
    }
    
    /**
     * @return int
     */
    public function getRange() : int
    {
        return $this->range;
    }

    /**
     * Hauteur max du plan
     * @return int
     */
    public function getHeight () : int {
        return ($this->getLevel()+1) * $this->getRange();
    }
    
    /**
     * Largeur max du plan
     * @return int
     */
    public function getWidth () : int {
        return (pow(3, $this->getLevel()) * $this->getRange());
    }
    
    /**
     * @param TernaryNode $node
     * @param int $foot
     * @return int
     */
    private function countLevel ($node, ?int $foot = null) : int {
        switch ($foot) {
            case TernaryNode::LEFT_CHILD:{
                if ($node->hasLeftChild()) {
                    return $this->countLeftLevel($node);
                }
            }break;
            case TernaryNode::MIDDLE_CHILD:{
                if ($node->hasMiddleChild()) {
                    return $this->countMiddleLevel($node);
                }
            }break;
            case TernaryNode::RIGHT_CHILD:{
                if ($node->hasRightChild()) {
                    return $this->countRightLevel($node);
                }
            }break;
            default:{
                $levels = array();//doit contenir au plus trois niveaux
                
                $levels[] = $this->countLeftLevel($node);
                $levels[] = $this->countMiddleLevel($node);
                $levels[] = $this->countRightLevel($node);
                
                $max = 0;
                foreach ($levels as $level) {
                    if ($level > $max) {
                        $max = $level;
                    }
                }
                return $max;
            }
        }
        return 0;
    }
    
    /**
     * @param TernaryNode $node
     * @return int
     */
    private function countLeftLevel ($node) : int{
        $count = 0;
        if ($node->hasLeftChild()) {
            $count++;
            
            $left = $node->getLeftChild();
            
            if (!$left->hasChilds()) {
                return $count;
            }
            $count++;//s'il a des anfants
            
            $childs = $left->getChilds();
            $levels = array();
            
            foreach ($childs as $child) {//comptage des niveau pour chaque noeud
                if ($child->hasChilds()) {
                    $level = $this->countLevel($child);
                    $levels[] = $level;
                }
            }
            
            $max = 0;
            foreach ($levels as $level) {//recherche du plus grand niveau
                if ($level > $max) {
                    $max = $level;
                }
            }
            
            $count += $max;//ajout le plus grand niveau 
        }
        return $count;
    }
    
    /**
     * @param TernaryNode $node
     * @return int
     */
    private function countMiddleLevel ($node) : int{
        $count = 0;
        if ($node->hasMiddleChild()) {
            $count++;
            
            $middle = $node->getMiddleChild();
            
            if (!$middle->hasChilds()) {
                return $count;
            }
            
            $count++;//s'il a des anfants
            
            $childs = $middle->getChilds();
            $levels = array();
            
            foreach ($childs as $child) {//comptage des niveau pour chaque noeud
                if ($child->hasChilds()) {
                    $level = $this->countLevel($child);
                    $levels[] = $level;
                }
            }
            
            $max = 0;
            foreach ($levels as $level) {//recherche du plus grand niveau
                if ($level > $max) {
                    $max = $level;
                }
            }
            
            $count += $max;//ajout le plus grand niveau
        }
        return $count;
    }
    
    /**
     * @param TernaryNode $node
     * @return int
     */
    private function countRightLevel ($node) : int{
        $count = 0;
        if ($node->hasRightChild()) {
            $count++;
            
            $right = $node->getRightChild();
            
            if (!$right->hasChilds()) {
                return $count;
            }
            
            $count++;//s'il a des anfants
            
            $childs = $right->getChilds();
            $levels = array();
            
            foreach ($childs as $child) {//comptage des niveau pour chaque noeud
                if ($child->hasChilds()) {
                    $level = $this->countLevel($child);
                    $levels[] = $level;
                }
            }
            
            $max = 0;
            foreach ($levels as $level) {//recherche du plus grand niveau
                if ($level > $max) {
                    $max = $level;
                }
            }
            
            $count += $max;//ajout le plus grand niveau
        }
        return $count;
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\Image2D\Mlm\TreeBuilder::process()
     */
    public function process () : void {
        $node = $this->getRoot();
        $root = $this->locate($node, 0, null);
        $this->drawables[] = $root;
    }
    
    /**
     * localisation d'un element desinable
     * @param TernaryNode $node
     * @param int $level
     * @param DrawableTernaryNode $parent
     */
    private function locate ($node, int $level, ?DrawableTernaryNode $parent) : DrawableTernaryNode {
        
        if ($parent == null) {//pour la racine 
            $drawable = new DrawableTernaryNode($node, 0, 0);
            if ($node->hasChilds()) {
                $childs = $node->getChilds();
                
                foreach ($childs as $child) {
                    $this->drawables[] = $this->locate($child, ($level+1), $drawable);
                }
            }
            
            return $drawable;
        }
        
        /**pour tout les autres
         * --------------------------------------------
         * 1. On calcule la distance etre le parent et son parent (pere et grand pere du noeud actuel)
         *     => A(Xparent, Yparent) le cordonnees du parent
         *     => B(X0, Yparent) les coordonneees d'un poit sur l'ax des Y
         *     => A et B apartien a une droite parallele a l'ax des X
         * 2. On calcule la variation de En:
         */
        
        $carreXaXb = pow($parent->getX() - ($parent->getNode()->isRoot()? 0 : $parent->getParent()->getX()), 2);
        $carreYaYb = 0;// car =>  pow($parent->getY() - $parent->getY(), 2);
        
        $dAB = pow(($carreXaXb + $carreYaYb), (1/2));//la distance entre A et B
        $En = $parent->getNode()->isRoot()? (pow($this->getLevel(), 2) * $this->getRange()) : ($parent->getNode()->isMiddleChild($node)? ($parent->getX()) :($dAB/3));
        $x = $parent->getNode()->isMiddleChild($node)? $parent->getX() : ($parent->getNode()->isRightChild($node)? $parent->getX()+$En : $parent->getX()-$En);//le poit x
        
        $y = $parent->getY() + $this->getRange();
        
        $drawable = new DrawableTernaryNode($node, $x, $y, ($level+1), $parent);
        
        //en fin on trace une linge
        $start = new Point($parent->getX(), $parent->getY());
        $end = new Point($x, $y);
        $this->lines[] = new DrawableLine($start, $end);
        
        if ($node->hasChilds()) {
            $childs = $node->getChilds();
            
            foreach ($childs as $child) {
                $this->drawables[] = $this->locate($child, ($level+1), $drawable);
            }
        }
        
        return $drawable;
    }
    
    /**
     * @return \Library\Image2D\Mlm\Ternary\DrawableTernaryNode[]
     */
    public function getDrawables()
    {
        if (empty($this->drawables)) {
            $this->process();
        }
        return $this->drawables;
    }

    /**
     * @return \Library\Image2D\DrawableLine[]
     */
    public function getLines()
    {
        if (empty($this->lines)) {
            $this->process();
        }
        return $this->lines;
    }


}

