<?php 

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

class BudgetRubric extends DBEntity {

    /**
     * @var string
     */
    private $label;
    
    /**
     * @var string
     */
    private $description;

    /**
     * @var Member
     */
    private $owner;

     /**
     * @var RubricCategory|null
     */
    private $category;


    /**
     * Get the value of label
     *
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
     * Get the value of owner
     *
     * @return  Member
     */ 
    public function getOwner() : ?Member
    {
        return $this->owner;
    }

    /**
     * Set the value of owner
     *
     * @param  Member|int  $owner
     */ 
    public function setOwner($owner) : void
    {
        if($owner instanceof Member || $owner == null) {
            $this->owner = $owner;
        } else if (self::isInt($owner)) {
            $this->owner = new Member(['id' => $owner]);
        } else {
            throw new PHPBackendException('invalide value in parametrer of setMember method');
        }
    }

    /**
     * Get the value of category
     *
     * @return  RubricCategory|null
     */ 
    public function getCategory() : ?RubricCategory
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @param  RubricCategory|null|int  $category
     */ 
    public function setCategory($category) : void
    {
        if ($category instanceof RubricCategory || $category == null) {
            $this->category = $category;
        } else if(self::isInt($category)) {
            $this->category = new RubricCategory(['id' => $category]);
        } else {
            throw new PHPBackendException('invalid arguement type in setCategory method');
        }
    }
}