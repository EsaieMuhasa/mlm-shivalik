<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Operation;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AbstractOperationFormValidator extends DefaultFormValidator
{
    const FIELD_AMOUNT = 'amount';
    
    /**
     * validation d'un montant
     * @param number $amount
     */
    protected  function validationAmount ($amount) : void {
        if ($amount == null) {
            throw new IllegalFormValueException("the amount is required");
        }else if (!preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
            throw new IllegalFormValueException("the amount must be a positive numeric value");
        }
    }
    
    /**
     * @param Operation $operation
     * @param number $amount
     */
    protected function processingAmount (Operation $operation, $amount) : void {
        try {
            $this->validationAmount($amount);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_AMOUNT, $e->getMessage());
        }
        
        $operation->setAmount($amount);
    }
}

