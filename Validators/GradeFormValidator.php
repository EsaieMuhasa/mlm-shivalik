<?php
namespace Validators;

use Library\AbstractFormValidator;
use Library\Config;
use Library\File;
use Library\IllegalFormValueException;
use Entities\Grade;
use Library\Image2D\ImageResizing;
use Library\Image2D\Image;
use Library\DAOException;
use Managers\GradeDAOManager;
use Managers\GenerationDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class GradeFormValidator extends AbstractFormValidator
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
    
    private function validationName ($name, $id = -1) : void {
        if ($name == null) {
            throw new IllegalFormValueException("the name is required");
        }
        
        try {
            if ($this->gradeDAOManager->nameExist($name, $id)) {
                throw new IllegalFormValueException("This name are used by oder grade");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function validationAmount ($amount, $id = -1) : void {
        if ($amount == null) {
            throw new IllegalFormValueException("the amount to pay is required");
        }else if (!preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
            throw new IllegalFormValueException("the amount to be paid must be a positive numeric value");
        }
    }
    
    private function validationIcon (File $icon) : void {
        if (!$icon->isFile()) {
            throw new IllegalFormValueException("the grade icon is mandatory");
        }
        
        $this->validationImage($icon);
    }
    
    private function validationPercentage ($percentage, $id = -1) : void {
        if ($percentage == null) {
            throw new IllegalFormValueException("the profit percentage is obligatory");
        }elseif (!preg_match(self::RGX_NUMERIC_POSITIF, $percentage)) {
            throw new IllegalFormValueException("the profit percentage must be a positive numeric value");
        }
        
        try {
            if ($this->gradeDAOManager->percentageExist($percentage, $id)) {
                throw new IllegalFormValueException("This percentage are used by oder grade {$id}");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function validationMaxGeneration ($maxGeneration) : void {
        try {
            if (!$this->generationDAOManager->idExist(intval($maxGeneration))) {
                throw new IllegalFormValueException("generation unknown in the system");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function processingName (Grade $grade, $name, $id=-1) : void {
        try {
            $this->validationName($name, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_NAME, $e->getMessage());
        }
        $grade->setName($name);
    }
    
    private function processingAmount (Grade $grade, $amount, $id=-1) : void {
        try {
            $this->validationAmount($amount, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_AMOUNT, $e->getMessage());
        }
        $grade->setAmount($amount);
    }
    
    
    private function processingMaxGeneration (Grade $grade, $maxGeneration) : void {
        try {
            $this->validationMaxGeneration($maxGeneration);
            $grade->setMaxGeneration($maxGeneration);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_MAX_GENERATION, $e->getMessage());
        }
    }
    
    private function processingIcon (Grade $grade, File $icon, bool $write = false) : void {
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
            $icon->getApplication()->writeFile($icon, $reelFullName);
            ImageResizing::profiling(new Image($reelName));
            $grade->setIcon($iconName);
        }
    }
    
    private function processingPercentage (Grade $grade, $percentage, $id=-1 ) : void {
        try {
            $this->validationPercentage($percentage, $id);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PERCENTAGE, $e->getMessage());
        }
        $grade->setPercentage($percentage);
    }

    /**
     * {@inheritDoc}
     * @see \Library\AbstractFormValidator::createAfterValidation()
     */
    public function createAfterValidation(\Library\HTTPRequest $request)
    {
         $grade = new Grade();
         $name = $request->getDataPOST(self::FIELD_NAME);
         $icon = $request->getFile(self::FIELD_ICON);
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
                 $this->processingIcon($grade, $icon, true);
                 $this->gradeDAOManager->updateIcon($grade->getId(), $grade->getIcon());
             } catch (DAOException $e) {
                 $this->setMessage($e->getMessage());
             }
         }
         $this->result = $this->hasError()? "grade registration failure":"grade registration success";
         
         return $grade;
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
        $grade = new Grade();
        $id = $request->getDataGET(self::CHAMP_ID);
        $name = $request->getDataPOST(self::FIELD_NAME);
        $icon = $request->getFile(self::FIELD_ICON);
        $percentage = $request->getDataPOST(self::FIELD_PERCENTAGE);
        $amount = $request->getDataPOST(self::FIELD_AMOUNT);
        $maxGeneration = $request->getDataPOST(self::FIELD_MAX_GENERATION);
        
        
        $this->traitementId($grade, $id);
        $this->processingName($grade, $name, $id);
        if ($icon->isFile()) {
            $this->processingIcon($grade, $icon);
        }
        $this->processingAmount($grade, $amount, $id);
        $this->processingPercentage($grade, $percentage, $id);
        $this->processingMaxGeneration($grade, $maxGeneration);
        
        if (!$this->hasError()) {
            try {
                $this->gradeDAOManager->update($grade, $grade->getId());
                if ($icon->isFile()){
                    $this->processingIcon($grade, $icon, true);
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
     * @param Config $config
     * @param int $id
     * @return string
     */
    public final static function getDataDirName (Config $config, int $id) : string{
        $fold = 'grades'.DIRECTORY_SEPARATOR.($id!=0? $id : '0');
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

