<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Office;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\DAOException;
use PHPBackend\Request;
use PHPBackend\File\UploadedFile;
use PHPBackend\Http\HTTPRequest;
use PHPBackend\Image2D\Image;
use PHPBackend\Image2D\ImageResizing;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;
use React\Dns\Config\Config;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeFormValidator extends DefaultFormValidator
{
    const FIELD_NAME = 'name';
    const FIELD_PHOTO = 'photo';
    const FIELD_MEMBER = 'member';
    const FIELD_LOCALISATION = 'localisation';
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * @var UploadedFile
     */
    private $photo;
    
    /**
     * @param string $name
     * @throws IllegalFormValueException
     */
    private function validationName ( $name ) : void {
        if ($name == null) {
            throw new IllegalFormValueException("Office name cannot be empty");
        }
    }
    
    private function validationPhoto (UploadedFile $photo, bool $onCreate=true) : void {
        if (!$photo->isFile() && $onCreate) {
            throw new IllegalFormValueException("select the desktop photo");
        }
        
        $this->validationImage($photo);
    }
    
    /**
     * Validation of member, sponsor of office
     * @param string $matricule, user id of member
     * @throws IllegalFormValueException
     */
    private function validationMember ($matricule) : void {
    	if ($matricule == null) {
    		throw new IllegalFormValueException("member ID is required");
    	}else {
    	    try {
    	        // le membre doit exister
        		if (!$this->memberDAOManager->checkByMatricule($matricule)) {
        			throw new IllegalFormValueException("unknown member ID in the system");
        		}
    	    } catch (DAOException $e) {
    	        throw new IllegalFormValueException($e->getMessage(), IllegalFormValueException::APP_LIB_ERROR_CODE, $e);
    	    }
    	}
    }
    
    private function processingName (Office $office, $name) : void {
        try {
            $this->validationName($name);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_NAME, $e->getMessage());
        }
        
        $office->setName($name);
    }
    
    /**
     * @param Office $office
     * @param string $matricule
     * @param boolean $onCreate
     * @param int $id
     */
    private function processingMember (Office $office, $matricule, $onCreate = true, $id=-1) : void  {
    	try {
    		
    		$this->validationMember($matricule);
    		$member = $this->memberDAOManager->findByMatricule($matricule);
    		
    		$office->setMember($member);
    		
    		if ($onCreate) {//pour la creation on verifien uniquement si le membre a deja un compte
    			if ($this->officeDAOManager->checkByMember($member->getId())) {
    				throw new IllegalFormValueException("the owner of this account already has");
    			}
    		}else {
    			$in = $this->officeDAOManager->checkByMember($member->getId());
    			if ($in->getId() != $id) {
    				//Identifiant different de l'id de l'office encours de modification???
    				//fermeture  de la faille de securite
    				throw new IllegalFormValueException ("the owner of this account already has");
    			}
    		}
    	} catch (IllegalFormValueException $e) {
    		$this->addError(self::FIELD_MEMBER, $e->getMessage());
    	}
    }
    
    /**
     * 
     * @param Office $office
     * @param UploadedFile $photo
     * @param bool $write
     * @param bool $onCreate
     */
    public function processingPhoto (Office $office, UploadedFile $photo, bool $write = false, bool $onCreate = true) : void {
        
    	try {
            $this->validationPhoto($photo, $onCreate);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PHOTO, $e->getMessage());
        }
        
        if ($write && $photo->isImage()) {
            $time = time();
            $reelName = self::getAbsolutDataDirName($photo->getApplication()->getConfig(), $office->getId()).DIRECTORY_SEPARATOR.$office->getId().'-'.$time.'-reel.'.$photo->getExtension();
            $reelFullName = self::getDataDirName($photo->getApplication()->getConfig(), $office->getId()).DIRECTORY_SEPARATOR.$office->getId().'-'.$time.'-reel.'.$photo->getExtension();
            $photoName = self::getDataDirName($photo->getApplication()->getConfig(), $office->getId()).DIRECTORY_SEPARATOR.$office->getId().'-'.$time.'.'.$photo->getExtension();
            $photo->getApplication()->writeUploadedFile($photo, $reelFullName);
            ImageResizing::profiling(new Image($reelName));
            $office->setPhoto($photoName);
        }
    }
    
    /**
	 * @return UploadedFile
	 */
	public function getPhoto() : ?UploadedFile {
		return $this->photo;
	}

	/**
     * @param HTTPRequest $request
     * @return Office
     */
    public function processingOffice (HTTPRequest $request) : Office {
    	$office = new Office();
    	$name = $request->getDataPOST(self::FIELD_NAME);
    	$photo = $request->getUploadedFile(self::FIELD_PHOTO);
    	$matricule = $request->getDataPOST(self::FIELD_MEMBER);
    	
    	$this->processingName($office, $name);
    	$this->processingPhoto($office, $photo);
    	$this->processingMember($office, $matricule);
    	
    	$form = new LocalisationFormValidator($this->officeDAOManager->getDaoManager());
    	$localisation = $form->processingLocalisation($request);
    	
    	$office->setLocalisation($localisation);
    	$this->addFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->toFeedback());
    	
    	$this->photo = $photo;
    	
    	if ($this->hasError() || $form->hasError()) {
    		$request->addAttribute($form::LOCALISATION_FEEDBACK, $form->toFeedback());
    	}
    	
    	$request->addAttribute(self::FIELD_LOCALISATION, $localisation);
    	
    	return $office;
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Office
     */
    public function createAfterValidation(Request $request)
    {
        $office = $this->processingOffice($request);
        
        if (!$this->hasError()) {
            try {
                $office->setCentral($request->getDataPOST('central') === 'central');
                $this->officeDAOManager->create($office);
                $this->processingPhoto($office, $this->photo, true);
                $this->officeDAOManager->updatePhoto($office->getId(), $office->getPhoto());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result =  $this->hasError()? "office registration failure":"office registration success";
        return $office;
    }


    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     */
    public function updateAfterValidation(Request $request)
    {
        $office = new Office();
        $id = $request->getAttribute(self::CHAMP_ID);
        $name = $request->getDataPOST(self::FIELD_NAME);
        $photo = $request->getUploadedFile(self::FIELD_PHOTO);
        
        $this->traitementId($office, $id);
        $this->processingName($office, $name);
        $this->processingPhoto($office, $photo, false, false);
        
        if (!$this->hasError()) {
            try {
                if ($photo->isImage()) {
                    $this->processingPhoto($office, $photo, true);
                }
            	$this->officeDAOManager->update($office, $office->getId());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result =  $this->hasError()? "failure to register changes to the office":"successful registration of office changes";
        return $office;
    }

    
    /**
     * recuperation du non du dossier qui confiendras les informations bruts d'un utilisateur
     * @param Config $config
     * @param int $id
     * @return string
     */
    public final static function getDataDirName (Config $config, int $id) : string{
        $fold = 'offices'.DIRECTORY_SEPARATOR.($id!=0? $id : '0');
        $dirPath = dirname(__DIR__).DIRECTORY_SEPARATOR.($config->get('webData')!=null? $config->get('webData') : 'Web').DIRECTORY_SEPARATOR.$fold;
        if (!is_dir($fold)) {
            @mkdir($dirPath, 0777, true);
        }
        
        return $fold;
    }
    
    
    /**
     * revoie le chemain absolute
     * @param Config $config
     * @param int $id
     * @return string
     */
    public final static function getAbsolutDataDirName(Config $config, int $id) : string {
        $fold= self::getDataDirName($config, $id);
        return dirname(__DIR__).DIRECTORY_SEPARATOR.($config->get('webData')!=null? $config->get('webData') : 'Web').DIRECTORY_SEPARATOR.$fold;
    }


}

