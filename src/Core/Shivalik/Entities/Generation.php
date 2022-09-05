<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 *
 * @author Esaie MHS
 *        
 */
class Generation extends DBEntity
{
    const MAX_GENERATION = 18;
    const MIN_GENERATION = 2;

    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $abbreviation;
    
    /**
     * @var int
     */
    private $number;
    
    /**
     * @var float
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
     * @return string
     */
    public function getAbbreviation() : ?string
    {
        return $this->abbreviation;
    }

    /**
     * @return number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return number
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $abbreviation
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;
    }

    /**
     * @param number $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @param number $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    
}

