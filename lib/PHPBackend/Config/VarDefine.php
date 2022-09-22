<?php
namespace PHPBackend\Config;

/** 
 * @author Esaie MHS
 * 
 */
class VarDefine  {

    /**
     * le nom de la variable
     * @var string
     */
    protected $name;
    
    /**
     * la valeur de la dite variable
     * @var mixed
     */
    protected $value;
    
    /**
     * @var string
     */
    protected $label;

    /**
     * @param string $name
     * @param mixed $value
     * @param string $label
     */
    public  function __construct(string $name, $value, ?string $label=null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
    }
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @return string
     */
    public function getLabel() : ?string
    {
        return $this->label;
    }


    public function __toString() : string {
        return $this->toString();
    }
    
    public function toString () : ?string {
        return $this->value;
    }

}

