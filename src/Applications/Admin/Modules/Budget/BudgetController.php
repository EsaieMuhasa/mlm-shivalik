<?php

namespace Applications\Admin\Modules\Budget;

use Core\Charts\BudgetConfigChartBuilder;
use Core\Shivalik\Managers\BudgetConfigDAOManager;
use Core\Shivalik\Managers\BudgetRubricDAOManager;
use Core\Shivalik\Managers\ConfigElementDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use Core\Shivalik\Validators\BudgetConfigFormValidator;
use Core\Shivalik\Validators\BudgetRubricFormValidator;
use Core\Shivalik\Validators\RubricCategoryFormValidator;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use PHPBackend\Http\HTTPController;
use PHPBackend\Request;
use PHPBackend\Response;

class BudgetController extends HTTPController {

    const ATTR_SESSION_BUDGET_CONFIG_ELEMENTS = 'BUDGET_CONFIG_ELEMENTS';

    /**
     * @var RubricCategoryDAOManager
     */
    private $rubricCategoryDAOManager;

    /**
     * @var BudgetRubricDAOManager
     */
    private $budgetRubricDAOManager;
    
    /**
     * @var BudgetConfigDAOManager
     */
    private $budgetConfigDAOManager;

    /**
     * @var ConfigElementDAOManager
     */
    private $configElementDAOManager;

    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;


    protected function init(Request $request, Response $response): void
    {
        $request->addAttribute(self::ATT_VIEW_TITLE, 'Budget setup');
        $request->addAttribute('config_nav', 'element');
    }


    /**
     * visualisation des configurations deja sauvegarder
     * (repartition encours d'utilisation)
     *
     * @param Request $request
     * @return void
     */
    public function executeIndex (Request $request) : void {
        $request->addAttribute('config_nav', 'home');
        $config = $this->budgetConfigDAOManager->checkAvailable()?  $this->budgetConfigDAOManager->findAvailable() : null;
        if ($config != null) {
            $elements = $this->configElementDAOManager->findByConfig($config->getId());
        } else {
            $elements = [];
        }
        $request->addAttribute('elements', $elements);
        $request->addAttribute('config', $config);
    }

    /**
     * generation du catalogue pour afficher le graphique de repartition de la configuration
     *
     * @param Request $request
     * @return void
     */
    public function executeConfigCatalogue (Request $request) : void {
        $config = $this->budgetConfigDAOManager->findAvailable();//findById($request->getDataGET('id'));
        $elements = $this->configElementDAOManager->findByConfig($config->getId());

        $builder = new BudgetConfigChartBuilder($request->getApplication()->getConfig(), [], $elements);
        $builder->getChart()->getConfig()->setType(ChartConfig::TYPE_DOUGHNUT_CHART);
        $request->addAttribute('chart', $builder->getChart());
    }

    /**
     * visualisation des elements de configuration
     *
     * @param Request $request
     * @return void
     */
    public function executeConfigElement (Request $request) : void {
        if($request->getDataGET('affichage') == 'categories') {
            $categories = $this->rubricCategoryDAOManager->countAll() != 0? $this->rubricCategoryDAOManager->findAll() : [];
            $request->addAttribute('categories', $categories);
        } else  {
            $rubrics = $this->budgetRubricDAOManager->countAll() != 0? $this->budgetRubricDAOManager->findAll() : [];
            foreach ($rubrics as $rubric) {
                if($rubric->getOwner() != null) {
                    $rubric->setOwner($this->memberDAOManager->findById($rubric->getOwner()->getId()));
                }
            }
            $request->addAttribute('rubrics', $rubrics);
        }
    }

    /**
     * selection des elemements a associer a une nouvelle configuration
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeSelectConfigElements (Request $request, Response $response) : void {
        if($request->getMethod() == Request::HTTP_POST) {
            $form = new BudgetRubricFormValidator($this->getDaoManager());
            $elements = $form->handleRequest($request);
            if(!$form->hasError()) {
                $request->getSession()->addAttribute(self::ATTR_SESSION_BUDGET_CONFIG_ELEMENTS, $elements);
                $response->sendRedirect('/admin/budget/new/validate-element-config');
            }
            
            $form->includeFeedback($request);
        } 

        $elements = $this->budgetRubricDAOManager->findAll();
        $request->addAttribute('elements', $elements);
    }
    
    /**
     * enregistrement definitive de la configuration et des element de ladite configuration
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeNewConfigElement (Request $request, Response $response) : void {
        $elements = $request->getSession()->getAttribute(self::ATTR_SESSION_BUDGET_CONFIG_ELEMENTS);
        if($elements == null || empty($elements)) {
            $response->sendRedirect('/admin/budget/new/select-element-config');
        }

        if($request->getMethod() == Request::HTTP_POST) {
            $form = new BudgetConfigFormValidator($this->getDaoManager());
            $form->createAfterValidation($request, $elements);
            if(!$form->hasError()) {
                $request->getSession()->removeAttribute(self::ATTR_SESSION_BUDGET_CONFIG_ELEMENTS);
                $response->sendRedirect("/admin/budget/");
            }

            $form->includeFeedback($request);
        }

        $request->addAttribute('elements', $elements);
    }

    /**
     * pour annueler l'operation de configuration du budget encours
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeCancelConfigElement (Request $request, Response $response) : void {
        $request->getSession()->removeAttribute(self::ATTR_SESSION_BUDGET_CONFIG_ELEMENTS);
        $response->sendRedirect("/admin/budget/");
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
            $response->sendRedirect("/admin/budget/new-category.html");
        }

        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new BudgetRubricFormValidator($this->getDaoManager());
            $rubric = $form->createAfterValidation($request);
            if (!$form->hasError()){
                $response->sendRedirect("/admin/budget/");
            }

            $form->includeFeedback($request);
            $request->addAttribute('rubric', $rubric);
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
    public function executeNewCategory (Request $request, Response $response) : void {
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