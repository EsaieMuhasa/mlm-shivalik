<?php

namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 * @author Esaie Muhasa
 * 
 * numerisation des elements de la fiche de vente.
 * pour que lorsqu'un membre achete de produit, que ce la soit manifeste dans son compte
 */
class SellSheetIRow extends DBEntity
{
    /**
     * reference du produit concerner
     * @var Product
     */
    private $product;

    /**
     * pour quel mois cette operation a ete faite?
     * @var MonthlyOrder
     */
    private $monthlyOrder;

    /**
     * le pris unitaire par defaut
     * @var float
     */
    private $unitPrice;

    /**
     * la quantite commander pour ledit produit
     * @var int
     */
    private $quantity;

    /**
     * Get la quantite commander pour ledit produit
     *
     * @return  int
     */ 
    public function getQuantity() : ?int
    {
        return $this->quantity;
    }

    /**
     * Set la quantite commander pour ledit produit
     * @param  float  $quantity  la quantite commander pour ledit produit
     */ 
    public function setQuantity(float $quantity) : void
    {
        $this->quantity = $quantity;
    }

    /**
     * Get le pris unitaire par defaut
     * @return  float
     */ 
    public function getUnitPrice() : ?float
    {
        return $this->unitPrice;
    }

    /**
     * Set le pris unitaire par defaut
     * @param  float  $unitPrice  le pris unitaire par defaut
     */ 
    public function setUnitPrice(float $unitPrice) : void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * Set pour quel mois cette operation a ete faite.
     * @param  MonthlyOrder|int  $monthlyOrder  pour quel mois cette operation a ete faite.
     */ 
    public function setMonthlyOrder( $monthlyOrder) : void
    {
        if ($monthlyOrder === null || $monthlyOrder instanceof MonthlyOrder) {
            $this->monthlyOrder = $monthlyOrder;
        } else if(self::isInteger($monthlyOrder)){
            $this->monthlyOrder = new MonthlyOrder(['id' => $monthlyOrder]);
        } else {
            throw new PHPBackendException('invalid value in setMonthlyOrder() : void parameter method');
        }
    }

    /**
     * Get reference du produit concerner
     * @return  Product
     */ 
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * Set reference du produit concerner
     * @param  Product  $product  reference du produit concerner
     */ 
    public function setProduct(Product $product) : void
    {
        $this->product = $product;
    }

    /**
     * Get pour quel mois cette operation a ete faite?
     *
     * @return  MonthlyOrder
     */ 
    public function getMonthlyOrder() : ?MonthlyOrder
    {
        return $this->monthlyOrder;
    }
}
