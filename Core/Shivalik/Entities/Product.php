<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Product extends DBEntity
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $description;
    
    /**
     * @var string
     */
    private $picture;
    
    /**
     * @var number
     */
    private $defaultUnitPrice;
    
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\DBEntity::__construct()
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

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
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPicture() : ?string
    {
        return $this->picture;
    }

    /**
     * @return number
     */
    public function getDefaultUnitPrice()
    {
        return $this->defaultUnitPrice;
    }

    /**
     * @param string $name
     */
    public function setName($name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) : void
    {
        $this->description = $description;
    }

    /**
     * @param string $picture
     */
    public function setPicture($picture) : void
    {
        $this->picture = $picture;
    }

    /**
     * @param number $defaultUnitPrice
     */
    public function setDefaultUnitPrice($defaultUnitPrice) : void
    {
        $this->defaultUnitPrice = $defaultUnitPrice;
    }

}

