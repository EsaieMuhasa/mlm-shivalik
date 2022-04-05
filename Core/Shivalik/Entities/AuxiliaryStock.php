<?php
namespace Core\Shivalik\Entities;

use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 * auxiliary stock managed by an office 
 * The owner office must be an auxiliary office.
 * when you user center office, in setOffice method, un InvalidArgumentException is up   
 */
class AuxiliaryStock extends Stock
{
    /**
     * Parent stock off this auxiliary stock
     * @var Stock
     */
    private $parent;
    
    /**
     * The office was control this auxiliary stock
     * @var Office
     */
    private $office;
    
    /**
     * @return \Core\Shivalik\Entities\Stock
     */
    public function getParent () : ?Stock
    {
        return $this->parent;
    }

    /**
     * @return \Core\Shivalik\Entities\Office
     */
    public function getOffice () : ?Office
    {
        return $this->office;
    }

    /**
     * tho set $parent stock in this auxiliary stock
     * @param \Core\Shivalik\Entities\Stock | int $parent
     */
    public function setParent ($parent) : void
    {
        if ($parent instanceof Stock || $parent == null) {
            $this->parent = $parent;
        } else if (self::isInt($parent)) {
            $this->parent = new Stock(['id' => $parent]);
        } else throw new PHPBackendException("Invalid value in setParent() param method");
    }

    /**
     * to set $office was control this auxiliary stock
     * @param \Core\Shivalik\Entities\Office | int $office
     */
    public function setOffice($office) : void
    {
        if ($office == null || $office instanceof Office) {
            $this->office = $office;
        } else if (self::isInt($office)) {
            $this->office = new Office(['id' => $office]);
        } else throw new PHPBackendException("Illegal argument value in setOffice method param");
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::getComment()
     */
    public function getComment(): ?string
    {
        return $this->parent->getComment();
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::getExpiryDate()
     * returned expiry date of this stock is the same of parent stock
     */
    public function getExpiryDate(): ?\DateTime
    {
        return $this->parent->getExpiryDate();
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::getProduct()
     * the product returned by this getter, is the same of parent stock 
     */
    public function getProduct(): ?Product
    {
        return $this->parent->getProduct();
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::getUnitPrice()
     * the unit price returned by this getter method, is the same of parent stock of this instance.
     * parent Stock cannot by null, as far as possible
     */
    public function getUnitPrice()
    {
        return $this->parent->getUnitPrice();
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::setComment()
     */
    public function setComment($comment): void
    {
        throw new PHPBackendException("setComment() => operation not supported");
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::setExpiryDate()
     */
    public function setExpiryDate($expiryDate): void
    {
        throw new PHPBackendException("setExpiredDate() => operation not supported");
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::setProduct()
     */
    public function setProduct($product)
    {
        throw new PHPBackendException("setProduct() => operation not supported");
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Entities\Stock::setUnitPrice()
     */
    public function setUnitPrice($unitPrice): void
    {
        throw new PHPBackendException("setUnitPrice() => operation not supported");
    }


}

