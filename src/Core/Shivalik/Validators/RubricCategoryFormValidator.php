<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Category;
use Core\Shivalik\Entities\RubricCategory;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use DateTime;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;

class RubricCategoryFormValidator extends DefaultFormValidator {

    const FIELD_LABEL = 'label';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_OWNABLE = 'ownable';

    /**
     * @var RubricCategoryDAOManager
     */
    private $rubricCategoryDAOManager;

    public function createAfterValidation(Request $request)
    {
        $category = new RubricCategory();

        $category->setLabel($request->getDataPOST(self::FIELD_LABEL));
        $category->setDescription($request->getDataPOST(self::FIELD_DESCRIPTION));
        $category->setOwnable($request->getDataPOST(self::FIELD_OWNABLE) == 'ownable');
    
        try {
            $category->setDateAjout(new DateTime());
            $this->rubricCategoryDAOManager->create($category);
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
        }
        $this->setResult("operation execution success", "operation execution failure");

        return $category;
    }

    public function updateAfterValidation(Request $request)
    {
        $category = new RubricCategory();

        return $category;
    }


}