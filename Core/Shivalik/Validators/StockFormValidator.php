<?php
namespace Core\Shivalik\Validators;

use PHPBackend\Validator\DefaultFormValidator;
use Core\Shivalik\Entities\Stock;
use PHPBackend\Validator\IllegalFormValueException;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Managers\StockDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class StockFormValidator extends DefaultFormValidator
{
    const FIELD_PRODUCT = 'product';
    const FIELD_COMMENT = 'comment';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_UNIT_PRICE = 'unitPrice';
    const FIELD_EXPIRY_DATE = 'expiryDate';
    const FIELD_MANUFACTURE_DATE = 'manufacturingDate';
    
    const MAX_LENGHT_COMMENT =  1000;//Longeur max du commentaire
    
    /**
     * @var StockDAOManager
     */
    private $stockDAOManager;
    
    /**
     * Validation du commentaire pour un stock
     * @param string $comment
     * @throws IllegalFormValueException
     */
    private function validationComment ($comment) : void {
        if ($comment != null && strlen($comment) > self::MAX_LENGHT_COMMENT) {
            throw new IllegalFormValueException("Commnet max lenght cannot succed ".self::MAX_LENGHT_COMMENT." characters");
        }
    }
    
    /**
     * Validation de la quantite initiale du stock
     * @param string|int $quantity
     * @throws IllegalFormValueException
     */
    private function validationQuantity ($quantity, ?int $id) : void {
        if ($quantity == null || $quantity === 0) {
            throw new IllegalFormValueException("the initial stock quantity cannot be empty");
        } else if (!preg_match(self::RGX_INT_POSITIF, $quantity)) {
            throw new IllegalFormValueException("the stock quantity must be a numeric value greater than zero");
        }
        
        if ($id !== null) {
            try {
                $stock = $this->stockDAOManager->load($id);
                if ($stock->getSold()< $quantity) {
                    throw new IllegalFormValueException("the minimum acceptable quantity must be {$stock->getSold()}");
                }
            } catch (DAOException $e) {
                throw new IllegalFormValueException($e->getMessage(), intval($e->getCode(), 10), $e);
            }
        }
    }
    
    /**
     * Validation du prix unitaire, d'une unite du stock
     * @param float $unitPrice
     * @throws IllegalFormValueException
     */
    private function validationUnitPrice ($unitPrice) : void {
        if ($unitPrice == null || $unitPrice === 0) {
            throw new IllegalFormValueException("the unit price is mandatory");
        } else if  (!preg_match(self::RGX_NUMERIC_POSITIF, $unitPrice)) {
            throw new IllegalFormValueException("the stock quantity must be a numeric value greater than zero");
        }
    }
    
    /**
     * Validation de la date d'expiration des produit du stock
     * @param string $expiryDate
     * @throws IllegalFormValueException
     */
    private function validationExpiryDate ($expiryDate) : void {
        if ($expiryDate == null) {
            throw new IllegalFormValueException("All pharmaceutical products must have an expiry date");
        } else if (!preg_match(self::RGX_DATE, $expiryDate)) {
            throw new IllegalFormValueException("Enter date in valid format");
        }
    }
    
    /**
     * Validation de la date de fabrication des produits du stock
     * @param string $manufacturingDate
     * @throws IllegalFormValueException
     */
    private function validationManufacturingDate ($manufacturingDate) : void {
        if ($manufacturingDate == null) {
            throw new IllegalFormValueException("All pharmaceutical products must have an manifaturing date");
        } else if (!preg_match(self::RGX_DATE, $manufacturingDate)) {
            throw new IllegalFormValueException("Enter date in valid format");
        }
        
        $now = new \DateTime();
        $date = new \DateTime($manufacturingDate);
        
        if ($now->getTimestamp() <= $date->getTimestamp()) {
            throw new IllegalFormValueException("The date of manufacture of this product is invalid");
        }
    }
    
    
    /**
     * Processuce de validation/traitement du commentaire d'un stock
     * @param string $comment
     * @param Stock $stock
     */
    private function processingComment($comment, Stock $stock) : void {
        try {
            $this->validationComment($comment);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_COMMENT, $e->getMessage());
        }
        
        $stock->setComment($comment);
    }
    
    /**
     * processuce de validation/traitement du quantite initiale d'un stock
     * @param string|int $quantity
     * @param Stock $stock
     * @param int $id dans le cas d'edition du stock
     */
    private function processingQuantity ($quantity, Stock $stock, ?int $id = null) : void {
        try {
            $this->validationQuantity($quantity, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_QUANTITY, $e->getMessage());
        }
        
        $stock->setQuantity($quantity);
    }
    
    /**
     * Processuce de validation/traitement du prix unitaire pour tout les produit du stock
     * @param string|float $unitPrice
     * @param Stock $stock
     */
    private function processingUnitPrice ($unitPrice, Stock $stock) : void {
        try {
            $this->validationUnitPrice($unitPrice);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_UNIT_PRICE, $e->getMessage());
        }
        $stock->setUnitPrice($unitPrice);
    }
    
    /**
     * Processuce de validation/traitement de la date d'expiration des produits appartenant a un stock
     * @param string $expiryDate
     * @param Stock $stock
     */
    private function processingExpiryDate ($expiryDate, Stock $stock) : void {
        try {
            $this->validationExpiryDate($expiryDate);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_EXPIRY_DATE, $e->getMessage());
        }
        
        $stock->setExpiryDate($expiryDate);
    }
    
    
    /**
     * processuce de validation/traitement de la date de fabrication des produit du stock
     * @param string $manufacturingDate
     * @param Stock $stock
     */
    private function processingManufacturingDate ($manufacturingDate, Stock $stock) : void {
        try {
            $this->validationManufacturingDate($manufacturingDate);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MANUFACTURE_DATE, $e->getMessage());
        }
        $stock->setManufacturingDate($manufacturingDate);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Stock
     */
    public function createAfterValidation(\PHPBackend\Request $request)
    {
        $stock = new Stock();
        
        $product = $request->getAttribute(self::FIELD_PRODUCT);
        $comment = $request->getDataPOST(self::FIELD_COMMENT);
        $quantity = $request->getDataPOST(self::FIELD_QUANTITY);
        $unitPrice = $request->getDataPOST(self::FIELD_UNIT_PRICE);
        $expiryDate = $request->getDataPOST(self::FIELD_EXPIRY_DATE);
        $manufacturingDate = $request->getDataPOST(self::FIELD_MANUFACTURE_DATE);
        
        $this->processingComment($comment, $stock);
        $this->processingQuantity($quantity, $stock);
        $this->processingUnitPrice($unitPrice, $stock);
        $this->processingExpiryDate($expiryDate, $stock);
        $this->processingManufacturingDate($manufacturingDate, $stock);
        
        $stock->setProduct($product);
        
        if (!$this->hasError()) {
            try {
                $this->stockDAOManager->create($stock);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Stock registration success for product {$stock->getProduct()->getName()}", "Product stock registration failure");
        
        return $stock;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return Stock
     */
    public function updateAfterValidation(\PHPBackend\Request $request)
    {
        $stock = new Stock();
        
        $product = $request->getAttribute(self::FIELD_PRODUCT);
        $id = intval($request->getDataGET(self::CHAMP_ID), 10);
        
        $comment = $request->getDataPOST(self::FIELD_COMMENT);
        $quantity = $request->getDataPOST(self::FIELD_QUANTITY);
        $unitPrice = $request->getDataPOST(self::FIELD_UNIT_PRICE);
        $expiryDate = $request->getDataPOST(self::FIELD_EXPIRY_DATE);
        $manufacturingDate = $request->getDataPOST(self::FIELD_MANUFACTURE_DATE);
        
        $this->processingComment($comment, $stock);
        $this->processingQuantity($quantity, $stock, $id);
        $this->processingUnitPrice($unitPrice, $stock);
        $this->processingExpiryDate($expiryDate, $stock);
        $this->processingManufacturingDate($manufacturingDate, $stock);
        
        $stock->setProduct($product);
        
        if (!$this->hasError()) {
            try {
                $this->stockDAOManager->update($stock, $id);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Stock registration success for product {$stock->getProduct()->getName()}", "Product stock registration failure");
        
        return $stock;
    }

}

