<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Category;
use Core\Shivalik\Managers\CategoryDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class CategoryValidator extends DefaultFormValidator
{
    
    const FIELD_TITLE  = 'title';
    const FIELD_DESCRIPTION = 'description';
    
    /**
     * @var CategoryDAOManager
     */
    private $categorieDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\DefaultFormValidator::validationId()
     */
    protected function validationId($id) : void {
        parent::validationId($id);
        try {
            if(!$this->categorieDAOManager->checkById($id))
                throw new IllegalFormValueException("Know ID in database");
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), intval($e->getCode(), 10), $e);
        }
    }
    
    /**
     * Validation du title d'un categorie
     * @param string $title
     * @throws IllegalFormValueException
     */
    private function validationTitle ($title) : void {
        if($title == null) {
            throw  new IllegalFormValueException("Title canot be empty");
        }
    }
    
    /**
     * Processuce de traitement du title d'une categorie
     * @param string $title
     * @param Category $categorie
     */
    private function processTitle ($title, Category $categorie) : void {
        try {
            $this->validationTitle($title);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_TITLE, $e->getMessage());
        }
        
        $categorie->setTitle($title);
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Category
     */
    public function createAfterValidation(\PHPBackend\Request $request)
    {
        $title = $request->getDataPOST(self::FIELD_TITLE);
        $description = $request->getDataPOST(self::FIELD_DESCRIPTION);
        $categorie = new Category();
        
        $this->processTitle($title, $categorie);
        $categorie->setDescription($description);
        
        if(!$this->hasError()) {
            try {
                $this->categorieDAOManager->create($categorie);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Success registration categorie", "Failure registration categirie");
        return $categorie;
        
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return Category
     */
    public function updateAfterValidation(\PHPBackend\Request $request)
    {
        $id = intval($request->getDataGET(self::FIELD_ID), 10);
        $title = $request->getDataPOST(self::FIELD_TITLE);
        $description = $request->getDataPOST(self::FIELD_DESCRIPTION);
        $categorie = new Category();
        
        $this->traitementId($categorie, $id);
        $this->processTitle($title, $categorie);
        $categorie->setDescription($description);
        
        if(!$this->hasError()) {
            try {
                $this->categorieDAOManager->update($categorie, $id);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->setResult("Success registration categorie", "Failure registration categirie");
        return $categorie;
    }

}

