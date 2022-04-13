<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 * 
 */
class Stock extends DBEntity
{
    /**
     * quantitie initinitial du stock
     * @var int
     */
    protected $quantity;
    
    /**
     * @var number
     */
    protected $unitPrice;
    
    /**
     * @var string
     */
    protected $comment;
    
    /**
     * date d'epiration du stock
     * @var \DateTime
     */
    protected $expiryDate;
    
    /**
     * La date de fabrication des produts du stock
     * @var \DateTime
     */
    protected $manufacturingDate;
    
    /**
     * @var Product
     */
    protected $product;
    
    /**
     * Collection des stocks auxiliaires
     * @var AuxiliaryStock[]
     */
    private $auxiliaries = [];
    
    /**
     * quantite deja servie
     * @var int
     */
    protected $served = 0;
    
    
    /**
     * @return number
     */
    public function getQuantity() : ?int {
        return $this->quantity;
    }

    /**
     * @return number
     */
    public function getUnitPrice() {
        return $this->unitPrice;
    }

    /**
     * @return string
     */
    public function getComment() : ?string {
        return $this->comment;
    }

    /**
     * @return \DateTime
     */
    public function getExpiryDate() : ?\DateTime {
        return $this->expiryDate;
    }

    /**
     * @param number $quantity
     */
    public function setQuantity($quantity) : void {
        $this->quantity = $quantity;
        $this->updateSold();
    }

    /**
     * @param number $unitPrice
     */
    public function setUnitPrice($unitPrice) : void {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) : void {
        $this->comment = $comment;
    }

    /**
     * @param \DateTime $expiryDate
     */
    public function setExpiryDate($expiryDate) : void {
        $this->expiryDate = $this->hydrateDate($expiryDate);
    }
    /**
     * @return \DateTime
     */
    public function getManufacturingDate() : ?\DateTime {
        return $this->manufacturingDate;
    }

    /**
     * @param \DateTime|string|int $manufacturingDate
     */
    public function setManufacturingDate($manufacturingDate) : void {
        $this->manufacturingDate = $this->hydrateDate($manufacturingDate);
    }

    /**
     * @return \Core\Shivalik\Entities\Product
     */
    public function getProduct() :?Product {
        return $this->product;
    }

    /**
     * @param \Core\Shivalik\Entities\Product | int $product
     * @throws PHPBackendException
     */
    public function setProduct($product) : void {
        if ($product == null || $product instanceof Product) {
            $this->product = $product;
        } else if (self::isInt($product)) {
            $this->product = new Product(['id' => $product]);
        } else {
            throw new PHPBackendException("invalide argument in setProduct param method");
        }
    }
    
    /**
     * @return \Core\Shivalik\Entities\AuxiliaryStock[] 
     */
    public function getAuxiliaries() {
        return $this->auxiliaries;
    }

    /**
     * @param multitype:\Core\Shivalik\Entities\AuxiliaryStock  $auxiliaries
     */
    public function setAuxiliaries (array $auxiliaries) : void {
        $this->auxiliaries = $auxiliaries;
        $this->updateSold();
    }
    
    /**
     * adding an auxiliary stock
     * @param AuxiliaryStock $stock
     * @throws PHPBackendException
     */
    public function addAuxiliary (AuxiliaryStock $stock) : void {
        if ($stock->getParent() != null && $stock->getParent()->getId() != $this->getId()) {
            throw new PHPBackendException("Auxiliary stock not support => {$stock->getParent()->getId()} != {$this->getId()}");
        }
        
        if ($stock->getParent() == null){
            $stock->setParent($this);
        }
        
        $this->auxiliaries[] = $stock;
        $this->served += $stock->getQuantity();
    }
    
    /**
     * ajout d'une collection des stocks
     * @param AuxiliaryStock ...$stocks
     * @return Stock
     */
    public function addAuxiliaries (AuxiliaryStock ...$stocks) : Stock {
        foreach ($stocks as $stock) {
            $this->addAuxiliary($stock);
        }
        return $this;
    }
    
    /**
     * Renvoie la quantite deja servie par le stock
     * @return int
     */
    public function getServed() : int {
        return $this->served;
    }

    /**
     * @param number $served
     */
    protected function setServed ($served)
    {
        $this->served = $served;
    }

    /**
     * renvoie la quantite disponible pour le stock
     * @return int
     */
    public function getSold () : int {
        return $this->quantity - $this->served;//quantite initial - quantite deja servies
    }
    
    /**
     * Renvoie la quaantite disponible en pourcentage
     * @return float
     */
    public function getSoldToPercent () : float {
        return $this->getSold() * 100 / $this->getQuantity();
    }
    
    /**
     * Mis en jour du sold, apres une operation X
     * @return void
     */
    private function updateSold () : void {
        $this->served = 0;
        foreach ($this->auxiliaries as $stock) {
            $this->served += $stock->getQuantity();
        }
    }

}

