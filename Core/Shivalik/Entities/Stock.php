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
    private $quantity;
    
    /**
     * @var number
     */
    private $unitPrice;
    
    /**
     * @var string
     */
    private $comment;
    
    /**
     * date d'epiration du stock
     * @var \DateTime
     */
    private $expiryDate;
    
    /**
     * @var Product
     */
    private $product;
    
    /**
     * @return number
     */
    public function getQuantity() : int
    {
        return $this->quantity;
    }

    /**
     * @return number
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return string
     */
    public function getComment() : ?string
    {
        return $this->comment;
    }

    /**
     * @return \DateTime
     */
    public function getExpiryDate() : ?\DateTime
    {
        return $this->expiryDate;
    }

    /**
     * @param number $quantity
     */
    public function setQuantity($quantity) : void
    {
        $this->quantity = $quantity;
    }

    /**
     * @param number $unitPrice
     */
    public function setUnitPrice($unitPrice) : void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) : void
    {
        $this->comment = $comment;
    }

    /**
     * @param \DateTime $expiryDate
     */
    public function setExpiryDate($expiryDate) : void
    {
        $this->expiryDate = $expiryDate;
    }
    /**
     * @return \Core\Shivalik\Entities\Product
     */
    public function getProduct() :?Product
    {
        return $this->product;
    }

    /**
     * @param \Core\Shivalik\Entities\Product | int $product
     */
    public function setProduct($product)
    {
        if ($product == null || $product instanceof Product) {
            $this->product = $product;
        } else if (self::isInt($product)) {
            $this->product = new Product(['id' => $product]);
        } else {
            throw new PHPBackendException("invalide argument in setProduct param method");
        }
    }


    
}

