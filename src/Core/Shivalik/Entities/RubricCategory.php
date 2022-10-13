<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 * classification des rubriques budgetaire
 */
class RubricCategory extends DBEntity {
    /**
     * @var string
     */
    private $label;

    /**
     * @var string|null
     */
    private $description;
    
    /**
     * @var boolean
     */
    private $ownable;

    /**
     * Get the value of description
     *
     * @return  string
     */ 
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     */ 
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    /**
     * Get the value of label
     * @return  string
     */ 
    public function getLabel() : ?string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @param  string  $label
     */ 
    public function setLabel(?string $label) : void
    {
        $this->label = $label;
    }

    /**
     * Get the value of ownable
     *
     * @return  boolean
     */ 
    public function isOwnable() : bool
    {
        return $this->ownable;
    }

    /**
     * Set the value of ownable
     *
     * @param  boolean|int  $ownable
     */ 
    public function setOwnable($ownable) : void
    {
        $this->ownable = $ownable;
    }
}