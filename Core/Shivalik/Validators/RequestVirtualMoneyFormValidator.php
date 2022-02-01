<?php

namespace Core\Shivalik\Validators;

use Applications\Member\MemberApplication;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class RequestVirtualMoneyFormValidator extends DefaultFormValidator {
	const FIELD_AMOUNT = 'amount';
	const FIELD_OFFICE = 'office';
	
	/**
	 * @var RequestVirtualMoneyDAOManager
	 */
	private $requestVirtualMoneyDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManger;
	
	/**
	 * validation du montant lors de l'envoie d'une requette
	 * @param number $amount
	 * @throws IllegalFormValueException
	 */
	private function validationAmount ($amount) : void {
		if ($amount == null || !preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
			throw new IllegalFormValueException("must be a numeric value greater than zero");
		}
	}
	
	/**
	 * processuce de traitement/validation du montant lors de l'enveoie d'une requette
	 * @param RequestVirtualMoney $rv
	 * @param number $amount
	 */
	private function processingAmount (RequestVirtualMoney $rv, $amount) : void {
		try {
			$this->validationAmount($amount);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_AMOUNT, $e->getMessage());
		}
		$rv->setAmount($amount);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
	 */
	public function createAfterValidation(Request $request) {
		$rv = new RequestVirtualMoney();
		$amount = $request->getDataPOST(self::FIELD_AMOUNT);
		$password = $request->getDataPOST('password');
		
		$this->processingAmount($rv, $amount);
		
	    if (sha1($password) != MemberApplication::getConnectedMember()->getPassword()) {
	        $this->addError('password', 'invalid password');
	    }
		if (!$this->hasError()) {
			try {
				$rv->setOffice($request->getAttribute(self::FIELD_OFFICE));
				$this->requestVirtualMoneyDAOManager->create($rv);
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
		    }
		}
		
		$this->result = $this->hasError()? "Failed to send request":"Successful sending of the request";
		return $rv;
	}

	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
	 */
	public function updateAfterValidation(Request $request) {
		// TODO Auto-generated method stub
		
	}


}

