<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Transfer;
use PHPBackend\Request;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class TransferFormValidator extends OperationFormValidator
{

    const FIELD_RECEIVER = 'receiver';
    
    private function validationReceiver ($receiver) : void {
        if ($receiver == null) {
            throw new IllegalFormValueException("the beneficiary member of the transaction is required");
        }elseif (!preg_match(self::RGX_INT_POSITIF, $receiver)){
            throw new IllegalFormValueException("the reference of the beneficiary member of the transaction must be a positive numeric value");
        }
    }
    
    private function processingReceiver (Transfer $transfer, $receiver) : void {
        try {
            $this->validationReceiver($receiver);
            $transfer->setReceiver($receiver);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_RECEIVER, $e->getMessage());
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     */
    public function createAfterValidation(Request $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(Request $request)
    {
        // TODO Auto-generated method stub
        
    }

}

