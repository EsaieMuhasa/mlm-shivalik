<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;

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

    
}

