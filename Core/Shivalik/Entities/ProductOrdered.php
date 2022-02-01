<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductOrdered extends DBEntity
{
    /**
     * @var Product
     */
    private $product;
    
    /**
     * @var number
     */
    private $reduction;
    
    /**
     * @var Commande
     */
    private $commande;
    
    /**
     * @var Stock
     */
    private $stock;
    /**
     * @return \Core\Shivalik\Entities\Product
     */
    public function getProduct() : ?Product
    {
        return $this->product;
    }

    /**
     * @return number
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * @return \Core\Shivalik\Entities\Commande
     */
    public function getCommande() : ?Commande
    {
        return $this->commande;
    }

    /**
     * @return \Core\Shivalik\Entities\Stock
     */
    public function getStock() : ?Stock
    {
        return $this->stock;
    }

    /**
     * @param \Core\Shivalik\Entities\Product $product
     */
    public function setProduct($product) : void
    {
        if ($product === null || $product instanceof Product) {
            $this->product = $product;
        } else if(self::isInt($product)) {
            $this->product = new Product(['id' => $product]);
        } else {
            throw new PHPBackendException("invalide value in setProduct () : void method parameter");
        }
    }

    /**
     * @param number $reduction
     */
    public function setReduction($reduction) : void
    {
        $this->reduction = $reduction;
    }

    /**
     * @param \Core\Shivalik\Entities\Commande $commande
     */
    public function setCommande($commande) : void
    {
        if ($commande === null || $commande instanceof Commande) {
            $this->commande = $commande;
        } elseif (condition) {
            $this->commande = new Commande(['id' => $commande]);
        } else {
            throw new PHPBackendException("invalide value in setCommande () : void method parameter");
        } 
    }

    /**
     * @param \Core\Shivalik\Entities\Stock $stock
     */
    public function setStock($stock) : void
    {
        if ($stock  === null || $stock instanceof Stock) {
            $this->stock = $stock;
        } elseif (self::isInt($stock)) {
            $this->stock = new Stock(['id' => $stock]);
        } else {
            throw new PHPBackendException("invalide value in setStock () : void method parameter");
        }
    }

}

