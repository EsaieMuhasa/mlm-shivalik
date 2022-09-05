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
abstract class OperationFormValidator extends DefaultFormValidator
{
    
    const FIELD_AMOUNT = 'amount';
    
    /**
     * validation du montant payer pour une operation
     * @param number $amount
     * @throws IllegalFormValueException
     */
    protected function validationAmount ($amount) : void {
        if ($amount == null ) {
            throw new IllegalFormValueException("Amount cannot be empty");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $amount)){
            throw new IllegalFormValueException("must be a positive numeric value");
        }        
    }    
    
    /***
     * processuce de traitement/validation du montant pour une operation donnees
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

