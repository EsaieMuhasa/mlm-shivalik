<?php
namespace Core\Shivalik\Validators;

use Applications\Member\Modules\Account\AccountController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Request;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class WithdrawalFormValidator extends AbstractOperationFormValidator
{
    const FIELD_OFFICE = 'office';
    const FIELD_PASSWORD = 'password';
    const FIELD_TELEPHONE = 'telephone';
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * @var WithdrawalDAOManager
     */
    private $withdrawalDAOManager;
    
    /**
     * @param number $office
     * @throws IllegalFormValueException
     */
    private function validationOffice ($office) : void {
        if ($office == null || !preg_match(self::RGX_INT_POSITIF, $office)) {
            throw new IllegalFormValueException("please select an office");
        }
        
//         else if (!preg_match(self::RGX_INT_POSITIF, $office)) {
//             throw new IllegalFormValueException("the reference must be a positive numeric value");
//         }
        
        try {
            if (!$this->officeDAOManager->checkById(intval($office, 10))) {
                throw new IllegalFormValueException("office unknown in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException("an error occurred in the data validation process {$e->getMessage()}", $e->getCode(), $e);
        }
    }
    
    /**
     * @param Member $member
     * @param string $password
     */
    private function validationPassword (Member $member, $password) : void {
        if ($password == null) {
            throw new IllegalFormValueException("confirm your password");
        }else if ($member->getPassword() != sha1($password)) {
            throw new IllegalFormValueException("invalid password");
        }
    }
    

    /**
     * @param string $telephone
     * @throws IllegalFormValueException
     */
    private function validationTelephone($telephone) : void {
    	if ($telephone == null) {
    		throw new IllegalFormValueException("enter your phone number");
    	}else if (!preg_match(self::RGX_TELEPHONE, $telephone) && !preg_match(self::RGX_TELEPHONE_RDC, $telephone)) {
    		throw new IllegalFormValueException("enter the phone number in valid format");
    	}
    }
    
    /**
     * @param Withdrawal $withdrawal
     * @param string $telephone
     */
    private function processingTelephone (Withdrawal $withdrawal, $telephone) : void {
    	try {
    		$this->validationTelephone($telephone);
    	} catch (IllegalFormValueException $e) {
    		$this->addError(self::FIELD_TELEPHONE, $e->getMessage());
    	}
    	$withdrawal->setTelephone($telephone);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\AbstractOperationFormValidator::validationAmount()
     */
    protected function validationAmount($amount): void
    {
        parent::validationAmount($amount);
        
        if (floatval($amount) < 10) {
            throw new IllegalFormValueException("You cannot withdraw less than 10 dolar");
        }
    }

    /**
     * 
     * @param Withdrawal $withdrawal
     * @param number $office
     */
    private function processingOffice (Withdrawal $withdrawal, $office) : void {
        try {
            $this->validationOffice($office);
            $withdrawal->setOffice($office);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_OFFICE, $e->getMessage());
        }
    }
    
    /**
     * @param Member $member
     * @param string $password
     */
    private function processingPassword (Member $member, $password) : void {
        try {
            $this->validationPassword($member, $password);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PASSWORD, $e->getMessage());
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Withdrawal
     */
    public function createAfterValidation(Request $request)
    {
        $withdrawal = new Withdrawal();
        $amount = $request->getDataPOST(self::FIELD_AMOUNT);
        $office = $request->getDataPOST(self::FIELD_OFFICE);
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        $telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
        
        $this->processingAmount($withdrawal, $amount);
        $this->processingOffice($withdrawal, $office);
        $this->processingTelephone($withdrawal, $telephone);
        
        /**
         * @var Account $account
         */
        $account = $request->getAttribute(AccountController::ATT_ACCOUNT);
        
        $withdrawal->setMember($account->getMember());   
        
        $this->processingPassword($account->getMember(), $password);
        if ($account->getSolde() <= $withdrawal->getAmount()) {
            $this->addError(self::FIELD_AMOUNT, "amount greater than the maximum withdrawable amount");
        } 
        
        if (!$this->hasError()) {
            try {
                $this->withdrawalDAOManager->create($withdrawal);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failure to send withdrawal request":"successful sending of the withdrawal request";
        return $withdrawal;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(Request $request)
    {
        $withdrawal = new Withdrawal();
        $office = $request->getDataPOST(self::FIELD_OFFICE);
        $telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
        $id = $request->getAttribute(self::CHAMP_ID);
        
        $this->processingTelephone($withdrawal, $telephone);
        $this->processingOffice($withdrawal, $office);
        
        if (!$this->hasError()) {
            try {
                $this->withdrawalDAOManager->update($withdrawal, $id);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failure to send withdrawal request":"successful sending of the withdrawal request";
        return $withdrawal;
    }
    
    /**
     * @param Request $request
     * @return Withdrawal
     */
    public function redirectAfterValidation(Request $request)
    {
        $withdrawal = new Withdrawal();
        $office = $request->getDataPOST(self::FIELD_OFFICE);
        $id = $request->getAttribute(self::CHAMP_ID);
        
        $this->processingOffice($withdrawal, $office);
        $withdrawal->setId($id);
        if (!$this->hasError()) {
            try {
                $this->withdrawalDAOManager->redirect($withdrawal);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "failure to redirect withdrawal request":"successful redirecting of the withdrawal request";
        return $withdrawal;
    }


}

