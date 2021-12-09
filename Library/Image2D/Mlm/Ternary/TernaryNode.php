<?php
namespace Library\Image2D\Mlm\Ternary;

use Library\Image2D\Mlm\Node;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface TernaryNode extends Node
{
    const LEFT_CHILD = 1;
    const MIDDLE_CHILD = 2;
    const RIGHT_CHILD = 3;
    
    /**
     * a-t-il un noeud afant a doit??
     * @return bool
     */
    public function hasLeftChild () : bool;
    
    /**
     * @return bool
     */
    public function hasRightChild () : bool;
    
    /**
     * @return bool
     */
    public function hasMiddleChild () : bool;
    
    /**
     * Est-ce ton afant directe de gauche??
     * @param TernaryNode $node
     * @return bool
     */
    public function isLeftChild ($node) : bool;
    
    /**
     * 
     * @param TernaryNode $node
     * @return bool
     */
    public function isRightChild ($node) : bool;
    
    /**
     * 
     * @param TernaryNode $node
     * @return bool
     */
    public function isMiddleChild ($node) : bool;
    
    
    /**
     * a-t-il un noeud afant a doit??
     * @return TernaryNode
     */
    public function getLeftChild ();
    
    /**
     * @return TernaryNode
     */
    public function getRightChild ();
    
    /**
     * @return TernaryNode
     */
    public function getMiddleChild ();
    
}

