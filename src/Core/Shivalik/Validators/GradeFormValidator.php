<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Grade;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use PHPBackend\Dao\DAOException;
use PHPBackend\File\FileManager;
use PHPBackend\File\UploadedFile;
use PHPBackend\Image2D\Image;
use PHPBackend\Image2D\ImageResizing;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeFormValidator extends DefaultFormValidator
{
    const FIELD_NAME = 'name';
    const FIELD_ICON = 'icon';
    const FIELD_PERCENTAGE = 'percentage';
    const FIELD_MAX_GENERATION = 'maxGeneration';
    const FIELD_AMOUNT = 'amount';
    
    /**
     * @var GenerationDAOManager
     */
    private $generationDAOManager;
    
    /**
     * @var GradeDAOManager
     */
    private $gradeDAOManager;
    
    /**
     * valisation du nom d'un grade ou d'un packet
     * @param string $name
     * @param int $id
     * @throws IllegalFormValueException
     */
    private function validationName ($name, $id = null) : void {
        if ($name == null) {
            throw new IllegalFormValueException("the name is required");
        }
        
        try {
            if ($this->gradeDAOManager->checkByName($name, $id)) {
                throw new IllegalFormValueException("This name are used by oder grade");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * validation du montant de qu'il faut payer pour s'affilier au packet
     * @param number $amount
     * @param int $id
     * @throws IllegalFormValueException
     */
    private function validationAmount ($amount, $id = -1) : void {
        if ($amount == null) {
            throw new IllegalFormValueException("the amount to pay is required");
        }else if (!preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
            throw new IllegalFormValueException("the amount to be paid must be a positive numeric value");
        }
    }
    
    
    /**
     * validation de l'icone d'un packet
     * @param UploadedFile $icon
     * @throws IllegalFormValueException
     */
    private function validationIcon (UploadedFile $icon) : void {
        if (!$icon->isFile()) {
            throw new IllegalFormValueException("the grade icon is mandatory");
        }
        
        $this->validationImage($icon);
    }
    
    /**
     * validation du pourcentage de sponsoring pour le membre affilier au packet
     * @param number $percentage
     * @param int $id
     * @throws IllegalFormValueException
     */
    private function validationPercentage ($percentage, $id = null) : void {
        if ($percentage == null) {
            throw new IllegalFormValueException("the profit percentage is obligatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $percentage)) {
            throw new IllegalFormValueException("the profit percentage must be a positive numeric value");
        }
        
        try {
            if ($this->gradeDAOManager->checkByPercentage($percentage, $id)) {
                throw new IllegalFormValueException("This percentage are used by oder grade {$id}");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * validation de la generation max qu'un membre affileir au packet peut atteindre
     * @param int $maxGeneration
     * @throws IllegalFormValueException
     */
    private function validationMaxGeneration ($maxGeneration) : void {
        try {
            if (!$this->generationDAOManager->checkById(intval($maxGeneration, 10))) {
                throw new IllegalFormValueException("generation unknown in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * processuce de valisdation/traitement du nom d'un packet
     * @param Grade $grade
     * @param string $name
     * @param int $id
     */
    private function processingName (Grade $grade, $name, $id=-1) : void {
        try {
            $this->validationName($name, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_NAME, $e->getMessage());
        }
        $grade->setName($name);
    }
    
    /**
     * processuce de validation/traitement du montant a payer pour s'affilier au packet
     * @param Grade $grade
     * @param number $amount
     * @param int $id
     */
    private function processingAmount (Grade $grade, $amount, $id=-1) : void {
        try {
            $this->validationAmount($amount, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_AMOUNT, $e->getMessage());
        }
        $grade->setAmount($amount);
    }
    
    /**
     * processuce de validation/traitement du generation max que peut atteindre la personne 
     * inscrit au packet
     * @param Grade $grade
     * @param int $maxGeneration
     */
    private function processingMaxGeneration (Grade $grade, $maxGeneration) : void {
        try {
            $this->validationMaxGeneration($maxGeneration);
            $grade->setMaxGeneration($maxGeneration);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MAX_GENERATION, $e->getMessage());
        }
    }
    
    /**
     * processuce de traitement/validation de l'image icone du packet
     * @param Grade $grade
     * @param UploadedFile $icon
     * @param bool $write faut-il directement l'ecrir sur le disque dur???
     */
    private function processingIcon (Grade $grade, UploadedFile $icon, bool $write = false, AppConfig $config=null) : void {
        try {
            $this->validationIcon($icon);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_ICON, $e->getMessage());
        }
        
        if ($write && $icon->isImage()) {
            $time = time();
            $reelName = self::getAbsolutDataDirName($icon->getApplication()->getConfig(), $grade->getId()).DIRECTORY_SEPARATOR.$grade->getId().'-'.$time.'-reel.'.$icon->getExtension();
            $reelFullName = self::getDataDirName($icon->getApplication()->getConfig(), $grade->getId()).DIRECTORY_SEPARATOR.$grade->getId().'-'.$time.'-reel.'.$icon->getExtension();
            $iconName = self::getDataDirName($icon->getApplication()->getConfig(), $grade->getId()).DIRECTORY_SEPARATOR.$grade->getId().'-'.$time.'.'.$icon->getExtension();
            FileManager::writeUploadedFile($icon, $reelFullName);
            ImageResizing::profiling(new Image($reelName));
            $grade->setIcon($iconName);
        }
    }
    
    /**
     * processuce de traitement/validation du pourcentage de sponsoring pour le membre affilier 
     * au packet
     * @param Grade $grade
     * @param number $percentage
     * @param int $id
     */
    private function processingPercentage (Grade $grade, $percentage, $id=-1 ) : void {
        try {
            $this->validationPercentage($percentage, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PERCENTAGE, $e->getMessage());
        }
        $grade->setPercentage($percentage);
    }

    /**
     * processuce de creation d'un nouveau grade
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Grade
     */
    public function createAfterValidation(Request $request)
    {
         $grade = new Grade();
         $name = $request->getDataPOST(self::FIELD_NAME);
         $icon = $request->getUploadedFile(self::FIELD_ICON);
         $percentage = $request->getDataPOST(self::FIELD_PERCENTAGE);
         $amount = $request->getDataPOST(self::FIELD_AMOUNT);
         $maxGeneration = $request->getDataPOST(self::FIELD_MAX_GENERATION);
         
         $this->processingName($grade, $name);
         $this->processingIcon($grade, $icon);
         $this->processingPercentage($grade, $percentage);
         $this->processingAmount($grade, $amount);
         $this->processingMaxGeneration($grade, $maxGeneration);
         
         if (!$this->hasError()) {
             try {
                 $this->gradeDAOManager->create($grade);
                 $this->processingIcon($grade, $icon, true, $request->getApplication()->getConfig());//ecriture de l'icone sur le serveur
                 $this->gradeDAOManager->updateIcon($grade->getId(), $grade->getIcon());
             } catch (DAOException $e) {
                 $this->setMessage($e->getMessage());
             }
         }
         $this->result = $this->hasError()? "grade registration failure":"grade registration success";
         
         return $grade;
    }

    /**
     * processuce de modification d'une occurence (d'un packet dans la bdd)
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return Grade
     */
    public function updateAfterValidation(Request $request)
    {
        $grade = new Grade();
        $id = $request->getDataGET(self::CHAMP_ID);
        $name = $request->getDataPOST(self::FIELD_NAME);
        $icon = $request->getUploadedFile(self::FIELD_ICON);
        $percentage = $request->getDataPOST(self::FIELD_PERCENTAGE);
        $amount = $request->getDataPOST(self::FIELD_AMOUNT);
        $maxGeneration = $request->getDataPOST(self::FIELD_MAX_GENERATION);
        
        
        $this->traitementId($grade, $id);
        $this->processingName($grade, $name, $id);
        if ($icon->isFile()) {//s'il faut modifier l'icone
            $this->processingIcon($grade, $icon);
        }
        $this->processingAmount($grade, $amount, $id);
        $this->processingPercentage($grade, $percentage, $id);
        $this->processingMaxGeneration($grade, $maxGeneration);
        
        if (!$this->hasError()) {
            try {
                $this->gradeDAOManager->update($grade, $grade->getId());
                if ($icon->isFile()){
                    $this->processingIcon($grade, $icon, true, $request->getApplication()->getConfig());//Ecriture de la nouvelle icone sur le disque dur du serveur
                    $this->gradeDAOManager->updateIcon($grade->getId(), $grade->getIcon());
                }
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->result = $this->hasError()? "failure to register grade changes":"successful registration of grade changes";
        
        return $grade;
    }

    /**
     * recuperation du non du dossier qui confiendras les informations bruts d'un utilisateur
     * @param AppConfig $config
     * @param int $id
     * @return string
     */
    public final static function getDataDirName (AppConfig $config, int $id) : string{
        $fold = 'grades'.DIRECTORY_SEPARATOR.($id!=0? $id : '0');
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

