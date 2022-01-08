<?php

namespace Core\Shivalik\Validators;

use Library\AbstractFormValidator;
use Library\IllegalFormValueException;
use Library\DAOException;
use Core\Shivalik\Managers\SizeDAOManager;
use Core\Shivalik\Entities\Size;

/**
 *
 * @author Esaie MHS
 *        
 */
class SizeFormValidator extends AbstractFormValidator {
	
	const FIELD_ABBREVIATION = 'abbreviation';
	const FIELD_NAME = 'name';
	const FIELD_PERCENTAGE = 'percentage';
	
	/**
	 * @var SizeDAOManager
	 */
	private $sizeDAOManager;
	
	/**
	 * @param string $abbreviation
	 * @param int $id
	 * @throws IllegalFormValueException
	 */
	private function validationAbbreviation ($abbreviation, $id=-1) : void {
		if ($abbreviation == null) {
			throw new IllegalFormValueException("abbreviation is required");
		}
		
		if (strlen($abbreviation) > 20) {
			throw new IllegalFormValueException("must not exceed 20 characters");
		}
		
		try {
			if ($this->sizeDAOManager->abbreviationExist($abbreviation, $id)) {
				throw new IllegalFormValueException("abbreviation already used");
			}
		} catch (DAOException $e) {
			throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	/**
	 * @param string $name
	 * @param int $id
	 * @throws IllegalFormValueException
	 */
	private function validationName ($name, $id=-1) : void {
		if ($name == null) {
			throw new IllegalFormValueException("full name is required");
		}
		
		if (strlen($name) > 20) {
			throw new IllegalFormValueException("must not exceed 150 characters");
		}
		
		try {
			if ($this->sizeDAOManager->nameExist($name, $id)) {
				throw new IllegalFormValueException("full name already used");
			}
		} catch (DAOException $e) {
			throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	/**
	 * @param number $percentage
	 * @throws IllegalFormValueException
	 */
	private function validationPercentage ($percentage) : void {
		if ($percentage == null) {
			throw new IllegalFormValueException("the bonus percentage is required");
		}
		
		if (!preg_match(self::RGX_NUMERIC_POSITIF, $percentage)) {
			throw new IllegalFormValueException("the values of this field are necessarily positive numeric");
		}
	}
	
	/**
	 * @param Size $size
	 * @param string $abbreviation
	 * @param number $id
	 */
	private function processingAbbreviation (Size $size, $abbreviation, $id = -1) : void {
		try {
			$this->validationAbbreviation($abbreviation, $id);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_ABBREVIATION, $e->getMessage());
		}
		$size->setAbbreviation($abbreviation);
	}
	
	/**
	 * @param Size $size
	 * @param string $name
	 * @param number $id
	 */
	private function processingName (Size $size, $name, $id = -1) : void {
		try {
			$this->validationName($name, $id);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_NAME, $e->getMessage());
		}
		$size->setName($name);
	}
	
	/**
	 * @param Size $size
	 * @param number $percentage
	 */
	private function processingPercentage (Size $size, $percentage) : void {
		try {
			$this->validationPercentage($percentage);
		} catch (IllegalFormValueException $e) {
			$this->addError(self::FIELD_PERCENTAGE, $e->getMessage());
		}
		$size->setPercentage($percentage);
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractFormValidator::createAfterValidation()
	 * @return Size
	 */
	public function createAfterValidation(\Library\HTTPRequest $request) {
		$size = new Size();
		$abbreviation = $request->getDataPOST(self::FIELD_ABBREVIATION);
		$name = $request->getDataPOST(self::FIELD_NAME);
		$percentage  = $request->getDataPOST(self::FIELD_PERCENTAGE);
		
		
		$this->processingAbbreviation($size, $abbreviation);
		$this->processingName($size, $name);
		$this->processingPercentage($size, $percentage);
		
		if (!$this->hasError()) {
			try {
				$this->sizeDAOManager->create($size);
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}
		
		$this->result = $this->hasError()? "registration failure" : "Successful registration";
		
		return $size;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Library\AbstractFormValidator::updateAfterValidation()
	 * @return Size
	 */
	public function updateAfterValidation(\Library\HTTPRequest $request) {
		$size = new Size();
		$id = $request->getAttribute(self::CHAMP_ID);
		$abbreviation = $request->getDataPOST(self::FIELD_ABBREVIATION);
		$name = $request->getDataPOST(self::FIELD_NAME);
		$percentage  = $request->getDataPOST(self::FIELD_PERCENTAGE);
		
		$this->processingAbbreviation($size, $abbreviation, $id);
		$this->processingName($size, $name,$id);
		$this->processingPercentage($size, $percentage);
		
		if (!$this->hasError()) {
			try {
				$this->sizeDAOManager->update($size, $id);
			} catch (DAOException $e) {
				$this->setMessage($e->getMessage());
			}
		}
		
		$this->result = $this->hasError()? "registration failure" : "Saving changes successfully";
		
		return $size;
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
	 * @see \Library\AbstractFormValidator::removeAfterValidation()
	 */
	public function removeAfterValidation(\Library\HTTPRequest $request) {
		// TODO Auto-generated method stub
		
	}

}

