<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\BudgetRubric;
use Core\Shivalik\Managers\BudgetRubricDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use DateTime;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

class BudgetRubricFormValidator extends DefaultFormValidator {

    const FIELD_LABEL = 'label';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_OWNER = 'owner';
    const FIELD_CATEGORY = 'category';

    /**
     * @var BudgetRubricDAOManager
     */
    private $budgetRubricDAOManager;
    /**
     * @var RubricCategoryDAOManager
     */
    private $rubricCategoryDAOManager;

    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;


    /**
     * insertion d'une tubirque budgetaire apres valdiation 
     * des donnees envoyer via le formulaire
     *
     * @param Request $request
     * @return BudgetRubric
     */
    public function createAfterValidation(Request $request) : BudgetRubric
    {
        $rubric = new BudgetRubric();
        $label = $request->getDataPOST(self::FIELD_LABEL);
        $description = $request->getDataPOST(self::FIELD_DESCRIPTION);
        $ownerKey = $request->getDataPOST(self::FIELD_OWNER);
        $categoryKey = $request->getDataPOST(self::FIELD_CATEGORY);

        $this->processingLabel($rubric, $label);
        $this->processingOwner($rubric, $ownerKey);
        $this->processingCategory($rubric, $categoryKey);
        $rubric->setDescription($description);

        if(!$this->hasError()) {
            if($rubric->getCategory()->isOwnable() && $rubric->getOwner() == null){
                $this->addError(self::FIELD_OWNER, "for the chosen category, the owner account is mandatory");
            }
            if(!$rubric->getCategory()->isOwnable() && $rubric->getOwner() == null){
                $this->addError(self::FIELD_OWNER, "for the chosen category, the owner account must be empty");
            }
        }

        if(!$this->hasError()) {
            $rubric->setDateAjout(new DateTime());
            try{
                $this->budgetRubricDAOManager->create($rubric);
            } catch (\Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }

        $this->setResult("operation execution success", "operation execution failure");

        return $rubric;
    }

    public function updateAfterValidation(Request $request) : BudgetRubric
    {
        $rubric = new BudgetRubric();
        return $rubric;
    }

    /**
     * validation du label d'une rubrique budgetaire
     *
     * @param ?string $label
     * @return void
     */
    protected function validationLabel ($label) : void {
        if($label == null )  {
            throw new IllegalFormValueException("label cannot be empty");
        } else if(strlen($label) > 255) {
            throw new IllegalFormValueException("label must not exceeed 255 characteres");
        } 
    }

    /**
     * validation du matricule du proprietarie de la rubrique du budget
     *
     * @param string $ownerKey
     * @return bool
     */
    protected function validationOwner ($ownerKey) : bool {
        if($ownerKey != null) {
            if(!$this->memberDAOManager->checkByMatricule($ownerKey)) {
                throw new IllegalFormValueException("Know member in database");
            }
            return true;
        }

        return false;
    }

    /**
     * validation dela reference aux elements de la table categoie
     *
     * @param string|int $categoryKey
     * @return void
     */
    protected function validationCategory ($categoryKey) : void {
        if($categoryKey == null) {
            throw new IllegalFormValueException("please select item category");
        } else if (!preg_match(self::RGX_INT_POSITIF, $categoryKey) || !$this->rubricCategoryDAOManager->checkById($categoryKey)) {
            throw new IllegalFormValueException("invalid reference");
        } 
    }

    /**
     * validation / traitement du label d'une rubrique budgetaire
     *
     * @param BudgetRubric $rubric
     * @param string $label
     * @return void
     */
    private function processingLabel (BudgetRubric $rubric, $label)  : void  {
        try {
            $this->validationLabel($label);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_LABEL, $e->getMessage());
        }

        $rubric->setLabel($label);
    }

    /**
     * traitement du membre proprietaire d'une rubrique
     *
     * @param BudgetRubric $rubric
     * @param string $ownerKey : matricule du membre
     * @return void
     */
    private function processingOwner (BudgetRubric $rubric, $ownerKey) : void {
        try {
            if($this->validationOwner($ownerKey)) {
                $owner = $this->memberDAOManager->findByMatricule($ownerKey);
                $rubric->setOwner($owner);
            }
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_OWNER, $e->getMessage());
        }
    }

    /**
     * validation /traitement de la categori d'une rubrique,
     * vous devez executer cette methode apres validation dy proprietaire du compte,
     * sous peine d'invalider la requette
     *
     * @param BudgetRubric $rubric
     * @param string|int $categoryKey
     * @return void
     */
    private function processingCategory (BudgetRubric $rubric, $categoryKey) : void {
        try{
            $this->validationCategory($categoryKey);
            $rubric->setCategory($this->rubricCategoryDAOManager->findById($categoryKey));
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_CATEGORY, $e->getMessage());
        }
    }
}