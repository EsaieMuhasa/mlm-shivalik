<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;


/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeBonus extends Operation
{
    
    /**
     * le montant virtuel qui lui ait permis d'avoir le bonus
     * @var VirtualMoney
     */
    private $virtualMoney;
    
    /**
     * le packet de l'office generateur du bonnus
     * @var OfficeSize
     */
    private $generator;

    /**
     * @return OfficeSize
     */
    public function getGenerator() : ?OfficeSize
    {
        return $this->generator;
    }

    /**
     * @param OfficeSize $generator
     */
    public function setGenerator($generator) : void 
    {
        if ($generator == null || $generator instanceof OfficeSize) {
            $this->generator = $generator;
        }elseif ($this->isInt($generator)){
            $this->generator = new OfficeSize(array('id' => $generator));
        }else {
            throw new PHPBackendException("invalid param value in param memthod setOfficeSize");
        }
    }
    
    /**
     * @return VirtualMoney
     */
    public function getVirtualMoney() : ?VirtualMoney
    {
        return $this->virtualMoney;
    }

    /**
     * @param VirtualMoney $virtualMoney
     */
    public function setVirtualMoney($virtualMoney)
    {
        if ($virtualMoney instanceof VirtualMoney || $virtualMoney == null) {
            $this->virtualMoney = $virtualMoney;
        } else if(self::isInt($virtualMoney)) {
            $this->virtualMoney = new VirtualMoney(array('id' => $virtualMoney));
        } else {
            throw new PHPBackendException("Invalid param method valeus");
        }
    }
  
}

