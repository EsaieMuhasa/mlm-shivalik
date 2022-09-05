<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class MoneyGradeMember extends DBEntity
{
    /**
     * Montant d'afiliation
     * @var number
     */
    private $afiliate = 0;
    
    /**
     * montant allouer aux produits
     * @var number
     */
    private $product = 0;
    
    /**
     * Le virtuel qui doit etre crediter
     * @var VirtualMoney
     */
    private $virtualMoney;
    
    /**
     * Le grade du membre concerner
     * @var GradeMember
     */
    private $gradeMember;
    /**
     * @return number
     */
    public function getAfiliate() : float {
        return $this->afiliate;
    }

    /**
     * @return number
     */
    public function getProduct() : float{
        return $this->product;
    }

    /**
     * @return \Core\Shivalik\Entities\VirtualMoney
     */
    public function getVirtualMoney() : ?VirtualMoney{
        return $this->virtialMoney;
    }

    /**
     * @return \Core\Shivalik\Entities\GradeMember
     */
    public function getGradeMember() : ?GradeMember {
        return $this->gradeMember;
    }

    /**
     * @param number $afiliate
     */
    public function setAfiliate(float $afiliate) : void{
        $this->afiliate = $afiliate;
    }

    /**
     * @param number $product
     */
    public function setProduct (float $product) : void {
        $this->product = $product;
    }

    /**
     * @param \Core\Shivalik\Entities\VirtualMoney $virtual
     */
    public function setVirtualMoney($virtual) : void {
        if ($virtual == null || $virtual instanceof VirtualMoney) {
            $this->virtialMoney = $virtual;
        } elseif (self::isInt($virtual)) {
            $this->virtialMoney = new VirtualMoney(['id' => $virtual]);
        } else {
            throw  new PHPBackendException("Ivalid arguement value in setVirtual() method parameter");
        }
    }

    /**
     * @param \Core\Shivalik\Entities\GradeMember $gradeMember
     */
    public function setGradeMember($gradeMember)  : void {
        if($gradeMember == null || $gradeMember instanceof GradeMember) {
            $this->gradeMember = $gradeMember;
        } elseif (self::isInt($gradeMember)) {
            $this->gradeMember = new GradeMember(['id' => $gradeMember]);
        } else {
            throw new PHPBackendException("Invalide argument value type in setGradeMember() method");
        }
    }

}

