<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\AuxiliaryStock;
use PHPBackend\Validator\IllegalFormValueException;
use PHPBackend\Dao\DAOException;
use Core\Shivalik\Entities\Stock;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;

/**
 * @author Esaie MUHASA
 * Validation des formulaire qui touchent le stock auxiliaire
 */
class AuxiliaryStockFormValidator extends StockFormValidator
{    
    const FIELD_PARENT = 'parent';
    const FIELD_OFFICE = 'office';
    
    /**
     * @var AuxiliaryStockDAOManager
     */
    private $auxiliaryStockDAOManager;
    
    /** 
     * validation du stock parent
     * @param string|int $parent
     * @throws IllegalFormValueException
     */
    private function validationParent ($parent) : void {
        if ($parent == null) {
            throw new IllegalFormValueException("Make sure you have selected a parent stock");
        } else if (!preg_match(self::RGX_INT_POSITIF, $parent)) {
            throw new IllegalFormValueException("The reference to the parent stock must be a positive integer");
        } else {
            try {
                if (!$this->stockDAOManager->checkById(intval($parent, 10))) {
                    throw new IllegalFormValueException("The reference of the parent stock is unknown in the system");
                }
            } catch (DAOException $e) {
                throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\StockFormValidator::validationQuantity()
     */
    protected function validationQuantity($quantity, ?int $id = null): void
    {
        parent::validationQuantity($quantity);
    }

    /**
     * Processsuce de validation/traitent du stock parent 
     * @param string|int $parent
     * @param AuxiliaryStock $stock
     */
    private function processingParent ($parent, AuxiliaryStock $stock, ?int $id = null) : void {
        try {
            $this->validationParent($parent);
            $stock->setParent($this->stockDAOManager->load(intval($parent, 10)));
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PARENT, $e->getMessage());
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\StockFormValidator::processingQuantity()
     * @param AuxiliaryStock $stock
     */
    protected function processingQuantity($quantity, Stock $stock, ?int $id = null): void {
        parent::processingQuantity($quantity, $stock, $id);
        if (!$this->hasError() && $stock->getParent()->getSold() < $stock->getQuantity()) {
            $this->addError(self::FIELD_QUANTITY, "The quantity available in stock cannot satisfy the order");
        }        
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\StockFormValidator::createAfterValidation()
     * @return AuxiliaryStock
     */
    public function createAfterValidation(\PHPBackend\Request $request)
    {
        $stock = new AuxiliaryStock();
        $quantity = $request->getDataPOST(self::FIELD_QUANTITY);
        $parent = $request->getDataPOST(self::FIELD_PARENT);
        $office = $request->getAttribute(self::FIELD_OFFICE);
        
        $this->processingParent($parent, $stock);
        $this->processingQuantity($quantity, $stock);
        $stock->setOffice($office);
        
        if (!$this->hasError()) {
            try {
                $this->auxiliaryStockDAOManager->create($stock);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Stock registration success for product {$stock->getProduct()->getName()}", "Product stock registration failure");
        
        return $stock;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\StockFormValidator::updateAfterValidation()
     * @return AuxiliaryStock
     */
    public function updateAfterValidation(\PHPBackend\Request $request) {
        $stock = new AuxiliaryStock();
        
        $id = intval($request->getDataGET(self::CHAMP_ID), 10);
        $quantity = $request->getDataPOST(self::FIELD_QUANTITY);
        $parent = $request->getDataPOST(self::FIELD_PARENT);
        $office = $request->getAttribute(self::FIELD_OFFICE);
        
        $this->processingParent($parent, $stock, $id);
        $this->processingQuantity($quantity, $stock, $id);
        $stock->setOffice($office);
        
        if (!$this->hasError()) {
            
            /**
             * @var \Core\Shivalik\Entities\AuxiliaryStock $old
             */
            $old = $this->auxiliaryStockDAOManager->load($id);
            if ($old->getServed() != 0 && $stock->getParent()->getId() != $old->getParent()->getId()) {
                $this->addError(self::FIELD_PARENT, "Unable to change parent stock for data integrity reasons");
            }
            
            try {
                $this->auxiliaryStockDAOManager->update($stock, $id);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Success saving changes", "Failed to save changes");
        
        return $stock;
    }

}

