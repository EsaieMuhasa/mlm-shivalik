<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\Image2D\Mlm\DefaultNodeIcon;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Grade extends DBEntity
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var number
     */
    private $percentage;
    
    /**
     * @var string
     */
    private $icon;
    
    /**
     * @var Generation
     */
    private $maxGeneration;
    
    /**
     * @deprecated
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $productAmount;

    /**
     * @var float
     */
    private $membershipAmount;

    /**
     * @var float
     */
    private $officeAmount;

    
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
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @return string
     */
    public function getIcon() : ?string
    {
        return $this->icon;
    }
    
    /**
     * 
     * @return DefaultNodeIcon
     */
    public function getIcons () : ?DefaultNodeIcon {
        if ($this->getIcon() != null) {
            return new DefaultNodeIcon($this->getIcon());
        }
        return null;
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
     * @param string $icon
     */
    public function setIcon($icon) : void
    {
        $this->icon = $icon;
    }
    
    /**
     * @return Generation
     */
    public function getMaxGeneration() : ?Generation
    {
        return $this->maxGeneration;
    }

    /**
     * @param Generation $maxGeneration
     */
    public function setMaxGeneration($maxGeneration) : void
    {
        if ($maxGeneration == null || $maxGeneration instanceof Generation) {
            $this->maxGeneration = $maxGeneration;
        }elseif ($this->isInt($maxGeneration)) {
            $this->maxGeneration = new Generation(array('id' =>  $maxGeneration));
        }else {
            throw new IllegalFormValueException("");
        }
    }
    
    
    /**
     * @deprecated
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @deprecated
     * @param number $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }




    /**
     * Get the value of officeAmount
     *
     * @return  float
     */ 
    public function getOfficeAmount()
    {
        return $this->officeAmount;
    }

    /**
     * Set the value of officeAmount
     *
     * @param  float  $officeAmount
     *
     * @return  self
     */ 
    public function setOfficeAmount(float $officeAmount)
    {
        $this->officeAmount = $officeAmount;

        return $this;
    }

    /**
     * Get the value of membershipAmount
     *
     * @return  float
     */ 
    public function getMembershipAmount()
    {
        return $this->membershipAmount;
    }

    /**
     * Set the value of membershipAmount
     *
     * @param  float  $membershipAmount
     *
     * @return  self
     */ 
    public function setMembershipAmount(float $membershipAmount)
    {
        $this->membershipAmount = $membershipAmount;

        return $this;
    }

    /**
     * Get the value of productAmount
     *
     * @return  float
     */ 
    public function getProductAmount()
    {
        return $this->productAmount;
    }

    /**
     * Set the value of productAmount
     *
     * @param  float  $productAmount
     *
     * @return  self
     */ 
    public function setProductAmount(float $productAmount)
    {
        $this->productAmount = $productAmount;

        return $this;
    }

    public function getSumAmount() {
        return ($this->getProductAmount() + $this->getOfficeAmount() + $this->getMembershipAmount());
    }
}

