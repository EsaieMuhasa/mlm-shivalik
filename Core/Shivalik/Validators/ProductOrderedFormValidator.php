<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\AuxiliaryStock;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\ProductOrdered;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;
use Core\Shivalik\Entities\Command;
use Applications\Office\Modules\Products\ProductsController;
use Core\Shivalik\Managers\ProductDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductOrderedFormValidator extends DefaultFormValidator {
    
    const FIELD_STOCKS = 'stocks';
    const FIELD_QUANTITIES = 'quantities';
    const FIELD_STOCK = 'stock';
    const FIELD_QUANTITY = 'quantity';
    
    
    /**
     * @var AuxiliaryStockDAOManager
     */
    private $auxiliaryStockDAOManager;
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * Validation des stocks lors de la preparation d'une commande et validation des quantitees
     * @param int $stock
     * @throws IllegalFormValueException
     */
    private function validationStock ($stock, Office $office) : void {
        if ($stock == null) {
            throw new IllegalFormValueException("tick a product and fill in the quantity");
        } else if (!preg_match(self::RGX_INT_POSITIF, $stock)) {
            throw new IllegalFormValueException("invalid index: {$stock}");
        }
        
        try {            
            if($this->auxiliaryStockDAOManager->checkById($stock)) {
                $in = $this->auxiliaryStockDAOManager->findById($stock);
                if ($in->getOffice()->getId() == $office->getId()) {
                    return;
                }
            }
            throw new IllegalFormValueException("no stock index by {$stock}");
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Validation de la quantite requise
     * @param int $quantity
     * @param AuxiliaryStock $stock
     * @throws IllegalFormValueException
     */
    private function validationQuantity ($quantity, ?AuxiliaryStock $stock = null) : void {
        if ($quantity == null ) {
            throw new IllegalFormValueException("the order quantity must not be null or empty");
        } elseif (!preg_match(self::RGX_INT_POSITIF, $quantity)) {
            throw new IllegalFormValueException("must be a positive integer");
        }
        
        if ($stock == null) {
            throw new IllegalFormValueException("an error occurred while validating the quantity");
        }
        
        if ($stock->getSold() < $quantity) {
            throw new IllegalFormValueException("the chosen stock cannot satisfy the order");
        }
    }
    
    /**
     * validaion/traitement du stock
     * @param int $stock
     * @param ProductOrdered $ordered
     */
    private function processingStock ($stock, ProductOrdered $ordered) : void {
        try {
            $this->validationStock($stock, $ordered->getCommand()->getOffice());
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_STOCK, $e->getMessage());
        }
    }
    
    /**
     * validation/traitement de la quantitee commander
     * @param int $quantity
     * @param ProductOrdered $ordered
     */
    private function processingQuantity ($quantity, ProductOrdered $ordered) : void {
        try {
            $this->validationQuantity($quantity, $ordered->getStock());
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_QUANTITY, $e->getMessage());
        }
    }
    
    /**
     * Validation de d'un rubrique de la commande
     * @param int $quantity
     * @param int $stock
     * @param Command $command
     * @return ProductOrdered
     */
    private function validationCommand ($quantity, $stock, Command $command) : ProductOrdered {
        $order = new ProductOrdered();
        $order->setCommand($command);
        
        try {
            $this->validationStock($stock, $command->getOffice());
            $order->setStock($this->auxiliaryStockDAOManager->load($stock));
            $order->setProduct($this->productDAOManager->findById($order->getStock()->getProduct()->getId()));
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_STOCK.$stock, $e->getMessage());
        }
        
        try {
            $this->validationQuantity($quantity, $order->getStock());
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_QUANTITY.$stock, $e->getMessage());
        }
        
        $order->setQuantity($quantity);
        return $order;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return ProductOrdered
     */
    public function createAfterValidation(Request $request) {
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(Request $request) {
        
    }
    
    /**
     * Preparation d'une commande
     * @param Request $request
     * @return ProductOrdered[]
     */
    public function prepareCommand (Request $request) {
        $orders = [];
        $stocks = $request->getDataPOST(self::FIELD_STOCKS);
        $command = $request->getSession()->getAttribute(ProductsController::ATT_COMMAND);
        
        if ($stocks != null && !empty($stocks)) {
            foreach ($stocks as $stock) {
                $quantity = intval($request->getDataPOST(self::FIELD_QUANTITY.$stock), 10);
                $orders[] = $this->validationCommand($quantity, $stock, $command);
            }
        } else {
            $this->setMessage("tick among the products offered to you");
        }
        
        
        $this->setResult("Operation Execution Success", "Failed to execute operation");
        
        return $orders;
    }


}

