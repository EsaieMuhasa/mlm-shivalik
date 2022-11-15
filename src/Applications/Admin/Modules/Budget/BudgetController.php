<?php

namespace Applications\Admin\Modules\Budget;

use Core\Charts\BudgetConfigChartBuilder;
use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Managers\BudgetConfigDAOManager;
use Core\Shivalik\Managers\BudgetRubricDAOManager;
use Core\Shivalik\Managers\ConfigElementDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\RubricCategoryDAOManager;
use Core\Shivalik\Managers\SubConfigElementDAOManager;
use Core\Shivalik\Validators\BudgetConfigFormValidator;
use Core\Shivalik\Validators\BudgetRubricFormValidator;
use Core\Shivalik\Validators\RubricCategoryFormValidator;
use Core\Shivalik\Validators\SubConfigElementFormValidator;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use PHPBackend\Http\HTTPController;
use PHPBackend\Request;
use PHPBackend\Response;

class BudgetController extends HTTPController {

    const ATTR_SESSION_BUDGET_CONFIG_ELEMENTS = 'BUDGET_CONFIG_ELEMENTS';
    const ATTR_SESSION_BUDGET_SUB_CONFIG_ELEMENTS = 'BUDGET_SUB_CONFIG_ELEMENTS';

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
     * @var SubConfigElementDAOManager
     */
    private $subConfigElementDAOManager;

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


    //sous configuration des rubrique budgetaire
    //===================================================

    /**
     * visualisation de l'actuel configuration de la sous configuration d'une rubirque budgetaire
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeSubConfigShow (Request $request, Response $response) : void {

        /** @var ConfigElement */
        $element = $this->configElementDAOManager->findById($request->getDataGET('id'));
        $element->setRubric($this->budgetRubricDAOManager->findById($element->getRubric()->getId()));

        if($this->subConfigElementDAOManager->checkByElement($element->getId())) {
            $items = $this->subConfigElementDAOManager->findByElement($element->getId());
            foreach ($items as $item) {
                $item->setRubric($this->budgetRubricDAOManager->findById($item->getRubric()->getId()));
            }
        } else {
            $items = [];
        }

        $request->addAttribute('element', $element);
        $request->addAttribute('items', $items);
    }

    /**
     * generation du catalogue du graphique de repartiton d'un item du budget gloable
     *
     * @param Request $request
     * @return void
     */
    public function executeSubConfigCatalogue (Request $request) : void {
        $config = $this->configElementDAOManager->findById($request->getDataGET('id'));
        $elements = $this->subConfigElementDAOManager->findByElement($config->getId());
        foreach ($elements as $item) {
            $item->setRubric($this->budgetRubricDAOManager->findById($item->getRubric()->getId()));
        }

        $builder = new BudgetConfigChartBuilder($request->getApplication()->getConfig(), [], $elements);
        $builder->getChart()->getConfig()->setType(ChartConfig::TYPE_DOUGHNUT_CHART);
        $request->addAttribute('chart', $builder->getChart());
    }

    /**
     * selection des elements de la sous configuration dela configurtion globale
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeSubConfigSelectConfigElements (Request $request, Response $response) : void {
        if($request->getMethod() == Request::HTTP_POST) {
            $form = new BudgetRubricFormValidator($this->getDaoManager());
            $elements = $form->handleRequest($request);
            if(!$form->hasError()) {
                $request->getSession()->addAttribute(self::ATTR_SESSION_BUDGET_SUB_CONFIG_ELEMENTS, $elements);
                $response->sendRedirect("/admin/budget/sub-config/{$request->getDataGET('id')}/new/validate-element-config");
            }
            
            $form->includeFeedback($request);
        } 

        $elements = $this->budgetRubricDAOManager->findAll();
        $request->addAttribute('elements', $elements);
    }

    /**
     * validation de la sous configuration de la repartition d'un element de la configuration globale
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeSubNewConfigElement (Request $request, Response $response) : void {
        $config = $this->configElementDAOManager->findById($request->getDataGET('id'));
        $elements = $request->getSession()->getAttribute(self::ATTR_SESSION_BUDGET_SUB_CONFIG_ELEMENTS);
        if($elements  == null || empty($elements)) {
            $response->sendRedirect("/admin/budget/sub-config/{$request->getDataGET('id')}/new/select-element-config");
        }

        if($request->getMethod() == Request::HTTP_POST) {
            $form = new SubConfigElementFormValidator($this->getDaoManager());
            $form->handleRequest($request, $config, $elements);
            if(!$form->hasError()) {
                $request->getSession()->removeAttribute(self::ATTR_SESSION_BUDGET_SUB_CONFIG_ELEMENTS);
                $response->sendRedirect("/admin/budget/sub-config/{$request->getDataGET('id')}/");
            }
            $form->includeFeedback($request);
        } 

        $request->addAttribute('elements', $elements);
    }

    /**
     * annulation dela sous configuration de la config gobale encours de realisation
     * cette action n'a pas de vue.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeCancelSubConfigElement (Request $request, Response $response) : void {
        $request->getSession()->removeAttribute(self::ATTR_SESSION_BUDGET_SUB_CONFIG_ELEMENTS);
        $response->sendRedirect("/admin/budget/sub-config/{$request->getDataGET('id')}/");
    }

}