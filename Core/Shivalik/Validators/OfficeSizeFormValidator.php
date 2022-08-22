<?php

namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\OfficeSize;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use PHPBackend\PHPBackendException;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeSizeFormValidator extends DefaultFormValidator {
	
	const FIELD_OFFICE = 'office';
	const FIELD_SIZE = 'size';
	
	/**
	 * @var SizeDAOManager
	 */
	private $sizeDAOManager;
	
	/**
	 * @var OfficeSizeDAOManager
	 */
	private $officeSizeDAOManager;
	
	/**
	 * @var OfficeDAOManager
	 */
	private $officeDAOManager;
	
	/**
	 * validation du packet de l'office
	 * @param number $size
	 * @throws IllegalFormValueException
	 */
	private function validationSize ($size) : void {
		if ($size == null) {
			throw new IllegalFormValueException("Office size is required");
		}else if (!preg_match(self::RGX_INT_POSITIF, $size)) {
			throw new IllegalFormValueException("Reference of office must be a positive numeric value");
		}
		
		try {
			if (!$this->sizeDAOManager->checkById($size)) {
				throw new IllegalFormValueException("unknown reference in the system");
			}
		} catch (DAOException $e) {
			throw new IllegalFormValueException($e->getMessage(),$e->getCode(), $e);
		}
	}
	
	/**
	 * processuce de traitement/validation du packet de l'office
	 * @param OfficeSize $os
	 * @param number $size
	 */
	private function processingSize (OfficeSize $os, $size) : void {
		try {
			$this->validationSize($size);
			$os->setSize($size);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_SIZE, $e->getMessage());
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
	 * @return OfficeSize
	 */
	public function createAfterValidation(Request $request) {
		$os = new OfficeSize();
		
		$size = $request->getDataPOST(self::FIELD_SIZE);
		
		$form = new OfficeFormValidator($this->getDaoManager());
		$office = $form->processingOffice($request);
		
		$os->setOffice($office);
		$this->processingSize($os, $size);
		
		if (!$this->hasError() && !$form->hasError()) {
			try {
				$os->setInitDate(new \DateTime());
				$this->officeSizeDAOManager->create($os);
				
				$form->processingPhoto($office, $form->getPhoto(), true, true, $request->getApplication()->getConfig());
				$this->officeDAOManager->updatePhoto($office->getId(), $office->getPhoto());
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}else {
			foreach ($form->getErrors() as $key => $error) {
				$this->addError($key, $error);
			}
		}
		
		$this->result = $this->hasError()? "failure to execute operations" : "successful execution of the operation";
		
		return $os;
	}
	
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(\PHPBackend\Request $request)
    {
        throw new PHPBackendException("you have not permission to perform this operation");
    }


}

