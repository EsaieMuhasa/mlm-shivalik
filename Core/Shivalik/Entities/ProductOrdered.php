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
     * @var Command
     */
    private $command;
    
    /**
     * @var AuxiliaryStock
     */
    private $stock;
    
    /**
     * La quantitee, pour la commande
     * @var int
     */
    private $quantity;
    
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
     * @return \Core\Shivalik\Entities\Command
     */
    public function getCommand() : ?Command
    {
        return $this->command;
    }

    /**
     * @return \Core\Shivalik\Entities\Stock
     */
    public function getStock() : ?AuxiliaryStock
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
     * @param \Core\Shivalik\Entities\Command $command
     */
    public function setCommand($command) : void
    {
        if ($command === null || $command instanceof Command) {
            $this->command = $command;
        } elseif (condition) {
            $this->command = new Command(['id' => $command]);
        } else {
            throw new PHPBackendException("invalide value in setCommand () : void method parameter");
        } 
    }

    /**
     * @param \Core\Shivalik\Entities\AuxiliaryStock $stock
     */
    public function setStock($stock) : void
    {
        if ($stock  === null || $stock instanceof AuxiliaryStock) {
            $this->stock = $stock;
        } elseif (self::isInt($stock)) {
            $this->stock = new AuxiliaryStock(['id' => $stock]);
        } else {
            throw new PHPBackendException("invalide value in setStock () : void method parameter");
        }
    }
    
    /**
     * @return number
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    /**
     * Renvoie le montant a payer pour cette commande
     * @return float
     */
    public function getAmount () : float {
        if ($this->quantity != null && $this->stock != null && $this->stock->getQuantity() != null) {
            return $this->quantity *  $this->stock->getUnitPrice();
        }
        return 0;
    }

    /**
     * @param number $quantity
     */
    public function setQuantity ($quantity) : void
    {
        $this->quantity = $quantity;
    }


}

