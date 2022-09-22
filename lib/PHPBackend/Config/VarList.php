<?php
namespace PHPBackend\Config;

/**
 *
 * @author Esaie MHS
 *        
 */
class VarList extends VarDefine implements \Countable
{
    /**
     * un text explicatif de la liste
     * @var string
     */
    protected $label;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Config\VarDefine::__construct()
     * @param string $label
     */
    public function __construct(string $name, $value, ?string $label=null)
    {
        parent::__construct($name, $value);
        $this->label = $label;

    }
    
    /**
     * {@inheritDoc}
     * @see \Countable::count()
     */
    public function count() : int
    {
        return count($this->getItems());
    }

    /**
     * Recuperation d'une item de la liste
     * @param string $name
     * @return VarDefine|NULL
     */
    public function getItem (string $name) : ?VarDefine {
        foreach ($this->getItems() as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }
        return null;
    }
    
    
    /**
     * Renvoie de l'item a l'index en parametre
     * @param int|string $index
     * @return VarDefine|NULL
     */
    public function get ($index) : ?VarDefine {
        $count = 0;
        foreach ($this->getItems() as $key => $item) {
            if ($index == $count || $index == $key || $item->getValue() == $index) {
                return $item;
            }
            $count++;
        }
        return null;
    }
    
    /**
     * @param string $name
     * @return VarDefine|NULL
     */
    public function find ($name) : ?VarDefine {
    	foreach ($this->getItems() as $item) {
    		if ( $item->getName() == $name) {
    			return $item;
    		}
    	}
    	return null;
    }
    
    /**
     * une collection des items de la liste
     * @return VarDefine[]
     */
    public function getItems () : array {
        return $this->getValue();
    }
    
    /**
     * @return string
     */
    public function getLabel() : ?string
    {
        return $this->label;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Config\VarDefine::__toString()
     */
    public function __toString(): string
    {
        return $this->label;
    }

      
}

