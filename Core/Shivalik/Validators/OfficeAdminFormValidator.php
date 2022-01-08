<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Entities\User;
use PHPBackend\DAOException;
use PHPBackend\Request;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeAdminFormValidator extends UserFormValidator
{
    const FIELD_OFFICE = 'office';
    const FIELD_LOCALISATION = 'localisation';    

    
	private function validationOffice ($office) : void {
        if ($office == null) {
            throw new IllegalFormValueException("the assignment office is mandatory");
        }elseif (!preg_match(self::RGX_INT_POSITIF, $office)) {
            throw new IllegalFormValueException("the reference of the assignment office must be a numeric value");
        }
        
        try {
            
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), IllegalFormValueException::APP_LIB_ERROR_CODE, $e);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::validationEmail()
     */
    protected function validationEmail($email, $id = null): void
    {
        if ($email == null) {
            throw new IllegalFormValueException('user email is required');
        }
        
        parent::validationEmail($email, $id);
        try {
            if ($this->officeAdminDAOManager->emailExist($email, intval($id))) {
                throw new IllegalFormValueException("email already assigned to another user");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function processingOffice (OfficeAdmin $admin, $office) : void {
        try {
            $this->validationOffice($office);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_OFFICE, $e->getMessage());
        }
        $admin->setOffice($office);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return OfficeAdmin
     */
    public function createAfterValidation(Request $request)
    {
        $user = new OfficeAdmin();
        $name = $request->getDataPOST(self::FIELD_NAME);
        $postName = $request->getDataPOST(self::FIELD_POST_NAME);
        $lastName = $request->getDataPOST(self::FIELD_LAST_NAME);
        $email = $request->getDataPOST(self::FIELD_EMAIL);
        $telephone = $request->getDataPOST(self::FIELD_TELEPHONE);
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        
        $photo = $request->getFile(self::FIELD_PHOTO);
        
        $this->processingName($user, $name);
        $this->processingPostName($user, $postName);
        $this->processingLastName($user, $lastName);
        $this->processingEmail($user, $email);
        $this->processingTelephone($user, $telephone);
        $this->processingPassword($user, $password);
        $this->processingPhoto($user, $photo);
        
        $user->setKind($request->getDataPOST(self::FIELD_KIND));
        
        $form = new LocalisationFormValidator($this->getDaoManager());
        $localisation = $form->processingLocalisation($request);
        
        $user->setLocalisation($localisation);
        $this->addFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->toFeedback());
        
        $request->addAttribute($form::LOCALISATION_FEEDBACK, $form->toFeedback());
        $request->addAttribute(self::FIELD_LOCALISATION, $localisation);
        
        if (!$this->hasError()) {
        	$user->setOffice($request->getAttribute(self::FIELD_OFFICE));
            try {
                $this->officeAdminDAOManager->create($user);
                $this->processingPhoto($user, $photo, true);
                $this->officeAdminDAOManager->updatePhoto($user->getId(), $user->getPhoto());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->result = $this->hasError()? "failure to execute the request" : "successful execution of the request";
        
        return $user; 
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(Request $request)
    {
        // TODO Auto-generated method stub
        
    }
	
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePasswordAfterValidation()
     */
    public function updatePasswordAfterValidation(Request $request): User {
    	$user = new OfficeAdmin();
    	$id = intval($request->getAttribute(self::CHAMP_ID), 10);
    	$password = $request->getDataPOST(self::FIELD_PASSWORD);
    	$confirmation = $request->getDataPOST(self::FIELD_CONFIRMATION);
    	
    	$this->processingPassword($user, $password, $confirmation);
    	$this->traitementId($user, $id);
    	
    	if (!$this->hasError()) {
    		try {
    			$this->officeAdminDAOManager->updatePassword($id, $user->getPassword());
    		} catch (DAOException $e) {
    			$this->setMessage($e->getMessage());
    		}
    	}
    	
    	$this->result = $this->hasError()? "password update failed":"password update success";
    	
    	return $user;   
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Validators\UserFormValidator::updatePhotoAfterValidation()
     */
    public function updatePhotoAfterValidation(Request $request): User {
    	$member = new OfficeAdmin();
    	$photo = $request->getFile(self::FIELD_PHOTO);
    	
    	if (!$photo->isFile()) {
    		$this->addError(self::FIELD_PHOTO, "make sure you have selected a photo on your terminal");
    	}
    	
    	$member->setId($request->getAttribute(self::CHAMP_ID));
    	$this->processingPhoto($member, $photo);
    	
    	if (!$this->hasError()) {
    		try {
    			$this->processingPhoto($member, $photo, true);
    			$this->officeAdminDAOManager->updatePhoto($member->getId(), $member->getPhoto());
    		} catch (DAOException $e) {
    			$this->setMessage($e->getMessage());
    		}
    	}
    	$this->result = $this->hasError()? "failed to update profile picture" : "profile picture update success";
    	return $member;
    }
    
}

