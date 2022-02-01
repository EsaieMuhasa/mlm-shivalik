<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\User;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeAdminDAOManager;
use PHPBackend\AppConfig;
use PHPBackend\Dao\DAOException;
use PHPBackend\File\FileManager;
use PHPBackend\File\UploadedFile;
use PHPBackend\Http\HTTPRequest;
use PHPBackend\Image2D\Image;
use PHPBackend\Image2D\ImageResizing;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class UserFormValidator extends DefaultFormValidator
{
    const FIELD_NAME = 'name';
    const FIELD_POST_NAME = 'postName';
    const FIELD_LAST_NAME = 'lastName';
    const FIELD_PSEUDO = 'pseudo';
    const FIELD_TELEPHONE = 'telephone';
    const FIELD_PASSWORD = 'password';
    const FIELD_CONFIRMATION = 'confirmation';
    const FIELD_PHOTO = 'photo';
    
    const FIELD_EMAIL = 'email';
    const FIELD_ENABLE = 'enable';
    const FIELD_KIND = 'kind';
    
    const MAX_LENGHT_NAME = 40;    
    const MIN_LENGHT_PASSWORD = 4;
    
    /**
     * @var MemberDAOManager
     */
    protected $memberDAOManager;
    
    /**
     * @var OfficeAdminDAOManager
     */
    protected $officeAdminDAOManager;    
    
    /**
     * validation du nom d'un utilisateur
     * @param string $name
     * @throws IllegalFormValueException
     */
    protected function validationName ($name) : void {
        if ($name == null) {
            throw new IllegalFormValueException("This field can not be empty");
        } else if (strlen($name)> self::MAX_LENGHT_NAME) {
            throw new IllegalFormValueException("the name cannot exceed ".self::MAX_LENGHT_NAME." squares");
        }
    }
        
    /**
     * validation du pseudo de connexion d'un utilisateur
     * @param string $pseudo
     * @param bool $onConnection
     * @param int $id
     * @throws IllegalFormValueException
     */
    protected function validationPseudo ($pseudo, bool $onConnection = false, $id = null) : void {
        if ($pseudo == null) {
            throw new IllegalFormValueException("Username can not be empty");
        } else if (strlen($pseudo)> self::MAX_LENGHT_NAME) {
            throw new IllegalFormValueException("the username cannot exceed ".self::MAX_LENGHT_NAME." squares");
        }
    }
    
    /**
     * validation du numero de telephone d'un utilisateur
     * @param string $telephone
     * @param int $id
     * @throws IllegalFormValueException
     */
    protected function validationTelephone ($telephone, $id = null) : void {
        if ($telephone == null) {
            //throw new IllegalFormValueException("the user's phone number is required");
        } else if (!preg_match(self::RGX_TELEPHONE, $telephone) && !preg_match(self::RGX_TELEPHONE_RDC, $telephone)) {
            throw new IllegalFormValueException("invalid phone number format");
        }
    }
    
    /**
     * validation du mot de passe d'un utilisatreur
     * @param string $password
     * @param string $confirmation
     * @param bool $onCreate
     * @throws IllegalFormValueException
     */
    protected function validationPassword ($password, $confirmation=null, bool $onCreate=true) : void {
        if ($onCreate) {
            if ($password == null) {
                throw new IllegalFormValueException("the user's password cannot be empty");
            }elseif (strlen($password) < self::MIN_LENGHT_PASSWORD) {
                throw new IllegalFormValueException("the password must have more than ".self::MIN_LENGHT_PASSWORD." characters");
            }
        }else {
            if ($password == null || $confirmation == null) {
                throw new IllegalFormValueException("Enter and confirm your password");
            }elseif (strlen($password) < self::MIN_LENGHT_PASSWORD) {
                throw new IllegalFormValueException("the password must have more than ".self::MIN_LENGHT_PASSWORD." characters");
            }elseif ($password != $confirmation){
                throw new IllegalFormValueException("the password must be identical to its confirmation");
            }
        }
    }
    
    /**
     * validation de la photo de profil d'un utilisateur
     * @param UploadedFile $photo
     * @throws IllegalFormValueException
     */
    protected function validationPhoto (UploadedFile $photo) : void {
        if (!$photo->isFile()) {
            throw new IllegalFormValueException("select profile photo");
        }
        
        $this->validationImage($photo);
    }
    
    /**
     * validation du mail d'un utilisateur
     * @param string $email
     * @param string $id
     * @throws IllegalFormValueException
     */
    protected function validationEmail ($email, $id = -1) : void {
        if ($email!=null && !preg_match(self::RGX_EMAIL, $email)) {
            throw new IllegalFormValueException("e-mail format is invalid");
        }
    }
    
    /**
     * processuce de triatement/validation du nom d'un utilisateur
     * @param User $user
     * @param string $name
     */
    protected function processingName (User $user, $name) : void {
        try {
            $this->validationName($name);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_NAME, $e->getMessage());
        }
        $user->setName($name);
    }
    
    /**
     * processuce de traitement/valisation du postnom d'un utilisateur
     * @param User $user
     * @param string $postName
     */
    protected function processingPostName (User $user, $postName) : void {
        try {
            $this->validationName($postName);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_POST_NAME, $e->getMessage());
        }
        $user->setPostName($postName);
    }
    
    /**
     * processuce de traitement/validation du prenom d'un utilisateur
     * @param User $user
     * @param string $lastName
     */
    protected function processingLastName (User $user, $lastName) : void {
        try {
            $this->validationName($lastName);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_LAST_NAME, $e->getMessage());
        }
        $user->setLastName($lastName);
    }
    
    /**
     * processuce de validation/traitement du pseudo d'un utilisateur
     * @param User $user
     * @param string $pseudo
     * @param bool $onConnection
     * @param int $id
     */
    protected function processingPseudo (User $user, $pseudo, bool $onConnection = false, $id = null) : void {
        try {
            $this->validationPseudo($pseudo, $onConnection, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PSEUDO, $e->getMessage());
        }
        $user->setPseudo($pseudo);
    }
    
    /**
     * processuce de traitement/validation du numero telephonique d'un utiilsateur
     * @param User $user
     * @param string $telephone
     * @param int $id
     */
    protected function processingTelephone (User $user, $telephone, $id = null) : void {
        try {
            $this->validationTelephone($telephone, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_TELEPHONE, $e->getMessage());
        }
        $user->setTelephone($telephone);
    }
    
    /**
     * processuce de traitemnt/validation du mail d'un utlisateur
     * @param User $user
     * @param string $email
     * @param int $id
     */
    protected function processingEmail (User $user, $email, $id = null) : void {
        try {
            $this->validationEmail($email, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_EMAIL, $e->getMessage());
        }
        $user->setEmail($email);
    }
    
    /**
     * processuce de traitement/validation du mot de passe d'un utilisateur
     * @param User $user
     * @param string $password
     * @param string $confirmation
     * @param bool $onCreate
     */
    protected function processingPassword (User $user, $password, $confirmation=null, bool $onCreate=true) : void{
        try {
            $this->validationPassword($password, $confirmation, $onCreate);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PASSWORD, $e->getMessage());
        }
        $user->setPassword(sha1($password));
    }
    
    /**
     * processuce de traitement de la photo de profil d'un utilisateur
     * @param User $user
     * @param UploadedFile $photo
     * @param bool $write
     * @param AppConfig $config
     */
    public function processingPhoto (User $user, UploadedFile $photo, bool $write=false, AppConfig $config = null) : void {
        try {
            $this->validationPhoto($photo);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PHOTO, $e->getMessage());
        }
        
        if ($write && $photo->isImage()) {
            $time = time();
            $reelName = self::getAbsolutDataDirName($config, $user->getId()).DIRECTORY_SEPARATOR.$user->getId().'-'.$time.'-reel.'.$photo->getExtension();
            $reelFullName = self::getDataDirName($$config, $user->getId()).DIRECTORY_SEPARATOR.$user->getId().'-'.$time.'-reel.'.$photo->getExtension();
            $photoName = self::getDataDirName($config, $user->getId()).DIRECTORY_SEPARATOR.$user->getId().'-'.$time.'.'.$photo->getExtension();
            FileManager::writeUploadedFile($photo, $reelFullName);
            ImageResizing::profiling(new Image($reelName));
            $user->setPhoto($photoName);
        }
    }
    
    /**
     * @param HTTPRequest $request
     * @return User
     */
    public abstract function updatePasswordAfterValidation (HTTPRequest $request) : User;
    
    /**
     * @param HTTPRequest $request
     * @return User
     */
    public abstract function updatePhotoAfterValidation (HTTPRequest $request) : User;
    
    /**
     * processuce de connection d'un utilisateur
     * @param HTTPRequest $request
     * @return User
     */
    public function connectionProcess (HTTPRequest $request) : User {
        
        $pseudo = $request->getDataPOST(self::FIELD_PSEUDO);
        $password = $request->getDataPOST(self::FIELD_PASSWORD);
        
        $user = new Member();
        
        $this->processingPassword($user, $password);
        
        if (!$this->hasError()) {
            try {
                
                /**
                 * @var User $u
                 */
                $u = null;
                if ($this->memberDAOManager->checkByPseudo($pseudo)) {//est-ce un membre
                    $u = $this->memberDAOManager->findByPseudo($pseudo, true);
                }elseif ($this->officeAdminDAOManager->checkByEmail($pseudo)){//est-ce un administrateur d'un bureau
                    $u = $this->officeAdminDAOManager->findByEmail($pseudo, true);
                }else {
                    $this->addError(self::FIELD_PSEUDO, "unknown user in the system");
                }
                
                $user->setPseudo($pseudo);
                
                if ($u != null) {
                    if ($u->getPassword() != $user->getPassword()) {
                        $this->addError(self::FIELD_PASSWORD, "incorrect password");
                    }else if(!$u->isEnable()){
                        $this->addError(self::FIELD_PSEUDO, "account is disable");
                    }else {
                        return $u;
                    }
                }
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? 'Connection failure':'';
        
        return $user;
    }
    
    /**
     * recuperation du non du dossier qui confiendras les informations bruts d'un utilisateur
     * @param AppConfig $config
     * @param int $id
     * @return string
     */
    public final static function getDataDirName (AppConfig $config, int $id) : string{
        $fold = 'users';
        $dirPath = dirname(__DIR__).DIRECTORY_SEPARATOR.($config->get('webData')!=null? $config->get('webData') : 'Web').DIRECTORY_SEPARATOR.$fold;
        if (!is_dir($fold)) {
            @mkdir($dirPath, 0777, true);
        }
        
        return $fold;
    }
    
    
    /**
     * revoie le chemain absolute
     * @param AppConfig $config
     * @param int $id
     * @return string
     */
    public final static function getAbsolutDataDirName(AppConfig $config, int $id) : string {
        $fold= self::getDataDirName($config, $id);
        return dirname(__DIR__).DIRECTORY_SEPARATOR.($config->get('webData')!=null? $config->get('webData') : 'Web').DIRECTORY_SEPARATOR.$fold;
    }
    
}

