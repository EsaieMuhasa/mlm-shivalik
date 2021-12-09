<?php
namespace Validators;

use Library\AbstractFormValidator;
use Library\IllegalFormValueException;
use Entities\Operation;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class OperationFormValidator extends AbstractFormValidator
{
    
    const FIELD_AMOUNT = 'amount';
    
    protected function validationAmount ($amount) : void {
        if ($amount == null ) {
            throw new IllegalFormValueException("Amount cannot be empty");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $amount)){
            throw new IllegalFormValueException("must be a positive numeric value");
        }        
    }    
    
    protected function processingAmount (Operation $operation, $amount) : void {
        try {
            $this->validationAmount($amount);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_AMOUNT, $e->getMessage());
        }
        $operation->setAmount($amount);
    }
}

