<?php

namespace Core\Shivalik\Validators;

use Applications\Member\Modules\MyOffice\MyOfficeController;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use DateTime;
use PHPBackend\PHPBackendException;

/**
 * @author Esaie Muhasa
 */
class RequestVirtualMoneyFormValidator extends DefaultFormValidator {
	
	/**
	 * @deprecated la depreciation de l'entite entraine cel du validateur
	 */
	const FIELD_AMOUNT = 'amount';
	const FIELD_OFFICE = 'office';
	const FIELD_AFFILIATION = 'affiliation';
	const FIELD_PRODUCT = 'product';
	
	/**
	 * @var RequestVirtualMoneyDAOManager
	 */
	private $requestVirtualMoneyDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManger;

	/**
	 * @var WithdrawalDAOManager
	 */
	private $withdrawalDAOManager;
	
	/**
	 * validation du montant lors de l'envoie d'une requette
	 * @param float $amount
	 * @throws IllegalFormValueException
	 */
	private function validationAmount ($amount) : void {
		if ($amount == null || !preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
			throw new IllegalFormValueException("must be a numeric value greater than zero");
		}
	}

	/**
	 * validation/traitement du montant d'affiliation
	 * @param RequestVirtualMoney $money
	 * @param float $amount
	 * @return self
	 */
	private function processingAffiliation (RequestVirtualMoney $money, $amount): self {
		try {
			$this->validationAmount($amount);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_AFFILIATION, $e->getMessage());
		}
		$money->setAffiliation($amount);
		return $this;
	}
	
	/**
	 * validation/traitement de montant prevue pour l'achat des produits
	 * @param RequestVirtualMoney $money
	 * @param mixed $amount
	 * @return self
	 */
	private function processingProduct (RequestVirtualMoney $money, $amount) : self {
		try {
			$this->validationAmount($amount);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_PRODUCT, $e->getMessage());
		}
		$money->setProduct($amount);
		return $this;
	}

	/**
	 * processuce d'envoie de matching deja servie par un office.
	 * Dans le cas où l'office est déjà envoyer alors o'operation ne seras pas realiser
	 */
	public function sendMatchingAfterValidation (Request $request) : RequestVirtualMoney {
		$virtual = new RequestVirtualMoney();

		$office = $request->getAttribute(MyOfficeController::ATT_OFFICE);
		if(!$this->withdrawalDAOManager->checkByOffice($office->getId(), true, false)){
			$this->setMessage('impossible to perform this operation because no matching is already served');
		} else {
			$virtual->setOffice($office);
			$virtual->setDateAjout(new DateTime());
			$withdrawals = $this->withdrawalDAOManager->findByOffice($office->getId(), true, false);
			$virtual->setWithdrawals($withdrawals);
			try {
				$this->requestVirtualMoneyDAOManager->create($virtual);
				$this->setMessage("Report dated {$virtual->getFormatedDateAjout('d F Y')}. You have sent a report of {$virtual->getProduct()} USD to the Shivalik company. Thank you for the trust you have in the Shivalik company.", false);
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}
		
		$this->setResult('Operation execution success', 'Failed to execute operation');
		return $virtual;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
	 */
	public function createAfterValidation(Request $request) {
		$rv = new RequestVirtualMoney();
		
		$product = $request->getDataPOST(self::FIELD_PRODUCT);
		$affiliation = $request->getDataPOST(self::FIELD_AFFILIATION);

		$password = $request->getDataPOST('password');
		
		$this
			->processingProduct($rv, $product)
			->processingAffiliation($rv, $affiliation);
		
	    if (sha1($password) != $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION)->getPassword()) {
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
		throw new PHPBackendException("unsuported operation");
	}


}

