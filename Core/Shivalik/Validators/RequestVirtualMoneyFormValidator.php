<?php

namespace Core\Shivalik\Validators;

use Library\AbstractFormValidator;
use Library\IllegalFormValueException;
use Library\DAOException;
use Applications\Member\MemberApplication;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Entities\Office;

/**
 *
 * @author Esaie MHS
 *        
 */
class RequestVirtualMoneyFormValidator extends AbstractFormValidator {
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
	 * @param number $amount
	 * @throws IllegalFormValueException
	 */
	private function validationAmount ($amount) : void {
		if ($amount == null || !preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
			throw new IllegalFormValueException("must be a numeric value greater than zero");
		}
	}
	
	/**
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
	 * @see \Library\AbstractFormValidator::createAfterValidation()
	 */
	public function createAfterValidation(\Library\HTTPRequest $request) {
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
	 * @see \Library\AbstractFormValidator::removeAfterValidation()
	 */
	public function removeAfterValidation(\Library\HTTPRequest $request) {
		$rv = new RequestVirtualMoney();
		
		$id = $request->getAttribute(self::CHAMP_ID);
		/**
		 * @var Office $office
		 */
		$office = $request->getAttribute(self::FIELD_OFFICE);
		
		$this->traitementId($rv, $id);
		
		if(!$this->hasError()) {
			try {
				$in = $this->requestVirtualMoneyDAOManager->getForId($id);
				if ($this->virtualMoneyDAOManger->hasResponse($id) || $in->getOffice()->getId() != $office->getId()) {
					$this->setMessage("Cannot perform this operation, because you no longer have the permission to do so");
				}else {					
					$this->requestVirtualMoneyDAOManager->remove($id);
				}
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}
		
		$this->result =  $this->hasError()? "failed to delete query"  :  "successful deletion of the request";
		
		return $rv;
	}

	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractFormValidator::deleteAfterValidation()
	 */
	public function deleteAfterValidation(\Library\HTTPRequest $request) {
		// TODO Auto-generated method stub
		
	}

	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractFormValidator::recycleAfterValidation()
	 */
	public function recycleAfterValidation(\Library\HTTPRequest $request) {
		// TODO Auto-generated method stub
		
	}

	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractFormValidator::updateAfterValidation()
	 */
	public function updateAfterValidation(\Library\HTTPRequest $request) {
		// TODO Auto-generated method stub
		
	}


}

