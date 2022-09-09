<?php

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

/**
 * @author Esaie MUHASA <esaiemuhasa.dev@gmail.com>
 * classe d'association entre une ligne de la fiche individuel les virtuels d'un office
 */
class SellSheetRowVirtualMoney extends DBEntity {

    /**
     * la fiche d'un membre
     *
     * @var SellSheetRow
     */
    private $sheet;

    /**
     * virtual d'un office
     *
     * @var VirtualMoney
     */
    private $money;

    /**
     * montant pris en compte
     *
     * @var float
     */
    private $amount;

    /**
     * Get la fiche d'un membre
     *
     * @return  SellSheetRow
     */ 
    public function getSheet() : ?SellSheetRow
    {
        return $this->sheet;
    }

    /**
     * Set la fiche d'un membre
     *
     * @param  SellSheetRow|int  $sheet  la fiche d'un membre
     */ 
    public function setSheet( $sheet) : void
    {
        $this->sheet = $sheet;
    }


    /**
     * Get virtual d'un office
     *
     * @return  VirtualMoney
     */ 
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set virtual d'un office
     *
     * @param  VirtualMoney|int  $money  virtual d'un office
     */ 
    public function setMoney($money) : void
    {
        $this->money = $money;
    }

    /**
     * Get montant pris en compte
     *
     * @return  float
     */ 
    public function getAmount() : ?float
    {
        return $this->amount;
    }

    /**
     * Set montant pris en compte
     *
     * @param  float  $amount  montant pris en compte
     */ 
    public function setAmount(?float $amount) : void
    {
        $this->amount = $amount;
    }
}