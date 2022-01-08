<?php
namespace Core\Shivalik\Validators;

use Library\AbstractFormValidator;
use Library\IllegalFormValueException;
use Library\DAOException;
use Library\HTTPRequest;
use Core\Shivalik\Managers\LocalisationDAOManager;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Entities\Localisation;

/**
 *
 * @author Esaie MHS
 *        
 */
class LocalisationFormValidator extends AbstractFormValidator
{
    
    const FIELD_COUNTRY = 'country';
    const FIELD_CITY = 'city';
    const FIELD_DISTRICT = 'district';
    const LOCALISATION_FEEDBACK = 'localisationFeedback';
    /**
     * ;
     * @var LocalisationDAOManager
     */
    private $localisationDAOManager;
    
    /**
     * @var CountryDAOManager
     */
    private $countryDAOManager;
    
    
    private function validationCountry ($country) : void {
        if ($country == null) {
            throw new IllegalFormValueException("country is required");
        }
        
        try {
            if (!$this->countryDAOManager->idExist(intval($country, 10))) {
                throw new IllegalFormValueException("the country you have selected is unknown in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function validationCity ($city) : void {
        if ($city == null) {
            throw new IllegalFormValueException("city name in country is required");
        }
    }
    
    private function processingCountry (Localisation $localisation, $country) : void {
        try {
            $this->validationCountry($country);
            $localisation->setCountry($this->countryDAOManager->getForId(intval($country)));
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_COUNTRY, $e->getMessage());
        }
    }
    
    private function processingCity (Localisation $localisation, $city) : void {
        try {
            $this->validationCity($city);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_CITY, $e->getMessage());
        }
        $localisation->setCity($city);
    }
    
    private function processingDistrict (Localisation $localisation, $district) : void {
        $localisation->setDistrict($district);
    }
    
    public function processingLocalisation (HTTPRequest $request) : Localisation {
        $localisation = new Localisation();
        $country = $request->getDataPOST(self::FIELD_COUNTRY);
        $city = $request->getDataPOST(self::FIELD_CITY);
        $district = $request->getDataPOST(self::FIELD_DISTRICT);
        
        $this->processingCity($localisation, $city);
        $this->processingCountry($localisation, $country);
        $this->processingDistrict($localisation, $district);
        
        return $localisation;
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::createAfterValidation()
     */
    public function createAfterValidation(\Library\HTTPRequest $request)
    {
        $localisation = $this->processingLocalisation($request);
        
        if (!$this->hasError()) {
            try {                
                $this->localisationDAOManager->create($localisation);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? 'registration failure' :'registration success';
        
        return $localisation;
        
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
        $localisation = $this->processingLocalisation($request);
        
        $id = $request->getAttribute(self::CHAMP_ID);
        $this->traitementId($localisation, $id);
        
        if (!$this->hasError()) {
            try {
                $this->localisationDAOManager->update($localisation, $localisation->getId());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? 'registration failure' :'registration success';
        
        return $localisation;
    }


}

