<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class Product extends DBEntity
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $description;
    
    /**
     * @var string
     */
    private $picture;
    
    /**
     * le prix unitaire par defaut d'un produit
     * @var number
     */
    private $defaultUnitPrice;
    
    /**
     * description de qauntification d'un produit 
     * @var string
     */
    private $packagingSize;
    
    /**
     * Categorisation du produit
     * @var Category
     */
    private $category;
    
    /**
     * stocks d'un produit
     * @var Stock[]
     */
    private $stocks = [];

    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @param int $limit
     * @return string
     */
    public function getDescription(?int $limit=null) : ?string
    {
        if ($limit !== null && $this->description !== null) {
            return substr($this->description, 0, $limit)."...";
        }
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPicture() : ?string
    {
        return $this->picture;
    }

    /**
     * @return number
     */
    public function getDefaultUnitPrice()
    {
        return $this->defaultUnitPrice;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    /**
     * @param string $picture
     */
    public function setPicture(?string $picture) : void
    {
        $this->picture = $picture;
    }

    /**
     * @param number $defaultUnitPrice
     */
    public function setDefaultUnitPrice ($defaultUnitPrice) : void
    {
        $this->defaultUnitPrice = $defaultUnitPrice;
    }
    
    /**
     * @return string
     */
    public function getPackagingSize() : ?string
    {
        return $this->packagingSize;
    }

    /**
     * @return \Core\Shivalik\Entities\Category
     */
    public function getCategory () : ?Category
    {
        return $this->category;
    }

    /**
     * @param string $packagingSize
     */
    public function setPackagingSize (?string $packagingSize) : void
    {
        $this->packagingSize = $packagingSize;
    }

    /**
     * @param \Core\Shivalik\Entities\Category | int $category
     * @throws PHPBackendException
     */
    public function setCategory($category) : void
    {
        if ($category instanceof Category || $category == null) {
            $this->category = $category;
        } else if (self::isInt($category)) {
            $this->category = new Category(['id' => $category]);
        } else {
            throw new PHPBackendException("Ivalid arguement in setCategie() param method");
        }
    }
    
    /**
     * @return \Core\Shivalik\Entities\Stock[] 
     */
    public function getStocks()
    {
        return $this->stocks;
    }

    /**
     * @param \Core\Shivalik\Entities\Stock[]  $stocks
     */
    public function setStocks(array $stocks)
    {
        $this->stocks = $stocks;
    }

    /**
     * Ajout d'un stock des produit pour le produit encours
     * @param Stock $stock
     * @throws PHPBackendException
     */
    public function addStock (Stock $stock) : void {
        if ($stock->getProduct() != null && $stock->getProduct()->getId() != $this->getId()) {
            throw new PHPBackendException("stock not belong to this product. -> {$this->getId()} != {$stock->getProduct()->getId()}");
        }
        
        if ($stock->getProduct() == null) {
            $stock->setProduct($this);
        }
        
        $this->stocks[] = $stock;
    }
    
    /**
     * ajout d'une collection des stocks pour le produit encours
     * @param Stock ...$stocks
     * @return Product
     */
    public function addStocks (Stock ...$stocks) : Product {
        foreach ($stocks as $stock) {
            $this->addStock($stock);
        }
        return $this;
    }

}

