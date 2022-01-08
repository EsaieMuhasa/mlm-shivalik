<?php
namespace PHPBackend\Image2D\Mlm;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class TreeBuilder
{
    /**
     * @var Node
     */
    protected $root;

    /**
     * constructeur d'initialisation
     * @param Node $root
     */
    public function __construct($root)
    {
        $this->root = $root;
    }
    
    /**
     * @return \PHPBackend\Image2D\Mlm\Node
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * Calculateur de niveau
     * @return int
     */
    public abstract function getLevel () : int;
    
    /**
     * lacement les traitement
     */
    public abstract function process () : void;

}

