<?php

namespace Applications\Admin\Modules\Budget;

use Core\Shivalik\Entities\RubricCategory;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use Core\Shivalik\Validators\BudgetRubricFormValidator;
use Core\Shivalik\Validators\BudgetRubricValidator;
use Core\Shivalik\Validators\RubricCategoryFormValidator;
use PHPBackend\Http\HTTPController;
use PHPBackend\Request;
use PHPBackend\Response;

class BudgetController extends HTTPController {

    /**
     * @var RubricCategoryDAOManager
     */
    private $rubricCategoryDAOManager;

    private $budgetRubricDAOManager;

    private $budgetConfigDAOManager;


    protected function init(Request $request, Response $response): void
    {
        $request->addAttribute(self::ATT_VIEW_TITLE, 'Budget setup');
    }


    /**
     * visualisation des configurations deja sauvegarder
     *
     * @param Request $request
     * @return void
     */
    public function executeIndex (Request $request) : void {

    }

    /**
     * insersion d'une nouvelle rubrique budgetaire
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeNewRubric (Request $request, Response $response) : void {
        if($this->rubricCategoryDAOManager->countAll() == 0) {
            $response->sendRedirect("/admin/budget/new-categorie.html");
        }

        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new BudgetRubricFormValidator($this->getDaoManager());
            $rubric = $form->createAfterValidation($request);
            if (!$form->hasError()){
                $response->sendRedirect("/admin/budget/");
            }

            $form->includeFeedback($request);
        } 

        $request->addAttribute('categories', $this->rubricCategoryDAOManager->findAll());
    }

    /**
     * insersion d'une nouvelle categorie des rubrique budgetaite
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeNewRubricCategory (Request $request, Response $response) : void {
        if($request->getMethod() == Request::HTTP_POST) {
            $form = new  RubricCategoryFormValidator($this->getDaoManager());
            $category = $form->createAfterValidation($request);
            if(!$form->hasError()) {
                $response->sendRedirect("/admin/budget/");
            }

            $form->includeFeedback($request);
        }
    }
}