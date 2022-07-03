<?php

namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class VirtualMoneyFormValidator extends DefaultFormValidator {
	
	const FIELD_AMOUNT = 'amount';
	const FIELD_PRODUCT = 'product';
	const FIELD_AFILIATE = 'afiliate';
	const FIELD_OFFICE = 'office';
	const FIELD_REQUEST_MONEY = 'request';
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManager;
	
	/**
	 * @var GradeMemberDAOManager
	 */
	private $gradeMemberDAOManager;
	
	/**
	 * @var OfficeSizeDAOManager
	 */
	private $officeSizeDAOManager;
	
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
	 * validation du montant d'afficliation
	 * @param number $amount
	 * @throws IllegalFormValueException
	 */
	private function validationAfiliateAmount ($amount) : void {
	    $this->validationAmount($amount);
	    
	    if (($amount % 20) != 0) {
	        throw new IllegalFormValueException("the affiliate amount must be a multiple of 20");
	    }
	}
	
	/**
	 * Validation du montant corespondant au produit acheter
	 * @param VirtualMoney $money
	 * @param number $product
	 */
	private function processingProductAmount (VirtualMoney $money, $product) : void {
	    try {
	        $this->validationAmount($product);
	    } catch (IllegalFormValueException $e) {
	        $this->addError(self::FIELD_PRODUCT, $e->getMessage());
	    }
	    $money->setProduct($product);
	}
	
	/**
	 * Validation du montant prevue pour les affiliations
	 * @param VirtualMoney $money
	 * @param number $afiliate
	 */
	private function processingAfiliateAmount (VirtualMoney $money, $afiliate) : void {
	    try {
	        $this->validationAfiliateAmount($afiliate);
	    } catch (IllegalFormValueException $e) {
	        $this->addError(self::FIELD_AFILIATE, $e->getMessage());
	    }
	    $money->setAfiliate($afiliate);
	}
	
	/**
	 * @param VirtualMoney $money
	 * @param number $amount
	 * @deprecated le amount n'est plus d'actualite, et sera suprimer dans la table d'ici quelque jours
	 */
	private function processingAmount (VirtualMoney $money, $amount) : void {
		try {
			$this->validationAmount($amount);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_AMOUNT, $e->getMessage());
		}
		$money->setAmount($amount);
		$money->setExpected($amount);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
	 * @return VirtualMoney
	 */
	public function createAfterValidation(Request $request) {
		$money = new VirtualMoney();
		$product = $request->getDataPOST(self::FIELD_PRODUCT);
		$afiliate = $request->getDataPOST(self::FIELD_AFILIATE);
		
		$this->processingAfiliateAmount($money, $afiliate);
		$this->processingProductAmount($money, $product);
		
		if (!$this->hasError()) {
			$money->setOffice($request->getAttribute(self::FIELD_OFFICE));
			try {			    
			    $generator = $this->officeSizeDAOManager->findCurrentByOffice($money->getOffice()->getId());//le packet actuel de l'office
			    
			    //calcul du %
			    $bonus = new OfficeBonus();
			    $bonus->setGenerator($generator);
			    $amountBonus = ($money->getProduct() / 100.0) * $generator->getSize()->getPercentage();
			    $bonus->setAmount($amountBonus);
			    //--calcul du %
			    
			    $bonus->setMember($money->getOffice()->getMember());
			    $bonus->setVirtualMoney($money);
			    
			    if ($amountBonus <= 0) {
			        $bonus = null;
			    }
			    
			    $money->setBonus($bonus);
			    
				$this->virtualMoneyDAOManager->create($money);
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}
		
		$this->result = $this->hasError()? "failed to send money":"";
		return $money;
	}

	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
	 */
	public function updateAfterValidation(Request $request) {
		throw new PHPBackendException("you cannot perform this operation");
	}



}

