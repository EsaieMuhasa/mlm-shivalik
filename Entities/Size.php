<?php
namespace Entities;

use Library\DBEntity;

/**
 *
 * @author Esaie MHS
 *        
 */
class Size extends DBEntity
{
    /**
     * @var string
     */
    private $abbreviation;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var double
     */
    private $percentage;
    
    /**
     * @return string
     */
    public function getName() : ?string 
    {
        return $this->name;
    }

    /**
     * @return number
     */
    public function getPercentage() : ?float
    {
        return $this->percentage;
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @param number $percentage
     */
    public function setPercentage($percentage) : void
    {
        $this->percentage = $percentage;
    }
    
    /**
     * @return string
     */
    public function getAbbreviation() : ?string
    {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     */
    public function setAbbreviation($abbreviation) : void
    {
        $this->abbreviation = $abbreviation;
    }


}

