<?php
namespace Validators;

use Library\IllegalFormValueException;
use Entities\Transfer;

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
     * @see \Library\AbstractFormValidator::createAfterValidation()
     */
    public function createAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::deleteAfterValidation()
     */
    public function deleteAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::recycleAfterValidation()
     */
    public function recycleAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::removeAfterValidation()
     */
    public function removeAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(\Library\HTTPRequest $request)
    {
        // TODO Auto-generated method stub
        
    }

}

