<?php
namespace Entities;

use Library\DBEntity;

/**
 *
 * @author Esaie MHS
 *        
 */
class Country extends DBEntity
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $abbreviation;
    
    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAbbreviation() : ?string
    {
        return $this->abbreviation;
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $abbreviation
     */
    public function setAbbreviation($abbreviation) : void
    {
        $this->abbreviation = $abbreviation;
    }

}

