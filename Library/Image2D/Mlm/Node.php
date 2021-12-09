<?php
namespace Library\Image2D\Mlm;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface Node
{
    /**
     * l'icone du noeud
     * @return NodeIcon
     */
    public function getIcon ();
    
    /**
     * y-a-il une icone pour ce noeud???
     * @return bool
     */
    public function hasIcon () : bool;
    
    /**
     * Revoie le noed parent
     * @return Node
     */
    public function getParent ();
    
    /**
     * Revoie le numero du pieds
     * @return int|NULL
     */
    public function getFoot () : ?int;
    
    /**
     * y-a-il un noeud parent
     * @return bool
     */
    public function hasParent () : bool;
    
    /**
     * est-ce une racine??
     * @return bool
     */
    public function isRoot () : bool;
    
    /**
     * renvoie le nombre des noeds enfants
     * @return int
     */
    public function countChilds () : int;
    
    /**
     * ce noeud a-t-elle des noeud fils???
     * @return bool
     */
    public function hasChilds () : bool;
    
    /**
     * @param int $foot
     * @return bool
     */
    public function hasChild (int $foot) : bool;
    
    /**
     * Renvoie le nom du noeud
     * @return string
     */
    public function getNodeName () : string;
    
    /**
     * revoie une collection des noeuds anfants
     * @return Node[]
     */
    public function getChilds ();
    
    /**
     * @param int $foot
     * @return Node
     */
    public function getChild (int $foot);
    
    /**
     * revoie les donnees acrocher au noeud
     * @return Object
     */
    public function getData ();
}

