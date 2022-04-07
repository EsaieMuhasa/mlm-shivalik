<?php
namespace Applications\Admin\Modules\Products;

use Applications\Admin\AdminController;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Validators\ProductValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Managers\StockDAOManager;
use Core\Shivalik\Validators\CategoryValidator;
use Core\Shivalik\Managers\CategoryDAOManager;
use Core\Shivalik\Entities\Product;
use Core\Shivalik\Validators\StockFormValidator;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductsController extends AdminController
{
    const ATT_CATEGORY = 'category';
    const ATT_CATEGORIES = 'categories';
    
    const ATT_PRODUCT ='product';
    const ATT_PRODUCTS ='products';
    const ATT_COUNT_PRODUCT = 'count_products';
    
    const ATT_STOCK ='stock';
    const ATT_STOCKS ='stocks';    
    
    //activation/desactivation des menus
    const ATT_ACTIVE_MENU = 'PRODUCT_ACTIVE_ITEM_MENU';
    const ITEM_MENU_DASHBOARD = 'ITEM_MENU_DASBOARD';
    const ITEM_MENU_PRODUCTS = 'ITEM_MENU_PRODUCT';
    const ITEM_MENU_STOCKS = 'ITEM_MENU_STOCK';
    const ITEM_MENU_OTHER_OPTERATIONS = 'ITEM_MENU_OTHER_OPERATIONS';
    const ITEM_MENU_ADD_PRODUCT = 'ITEM_MENU_ADD_PRODUCT';
    //--
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * @var CategoryDAOManager
     */
    private $categoryDAOManager;
    
    /**
     * @var StockDAOManager
     */
    private $stockDAOManager;
    
    
    /**
     * {@inheritDoc}
     * @see \Applications\Admin\AdminController::__construct()
     */
    public function __construct(Application $application, string $module, string $action)
    {
        parent::__construct($application, $module, $action);
        $application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Products");
        $application->getRequest()->addAttribute(self::ATT_COUNT_PRODUCT, $this->productDAOManager->countAll());
    }
    
    /**
     * Initialisation des attributs utiles pour le sous menu product
     * @param Request $erquest
     */
    private function itemMenuProduct (Request $request) : void {
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_PRODUCTS);
    }
    
    private function itemMenuOtherOperations (Request $request) : void {
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_OTHER_OPTERATIONS);
    }

    /**
     * Affichage des produits
     * @param Request $request
     * @param Response $response
     */
    public function executeShowProducts (Request $request, Response $response) : void {
        $limit = $request->existInGET('limit')? intval($request->getDataGET('limit'), 10) : 12;
        $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        $affichage = $request->existInGET('affichage')? $request->getDataGET('affichage') : 'table';
        $count = $this->productDAOManager->countAll();
        
        if ($this->productDAOManager->checkAll($limit, $offset)) {
            $products = $this->productDAOManager->findAll($limit, $offset);
        } else {
            if ($count !=0 ) {
                $response->sendError();
            }
            
            $products = [];
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
        $request->addAttribute(self::ATT_COUNT_PRODUCT, $count);
        $request->addAttribute('affichage', $affichage);
        $this->itemMenuProduct($request);
    }
    
    /**
     * dashboad de gestion des produits
     * @param Request $request
     */
    public function executeIndex (Request $request ) : void {
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_DASHBOARD);
    }
    
    /**
     * Visualisation de la description d'un produit
     * on en profite pour directement afficher les autres produits de la meme categorie
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeProduct (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        $limit = $request->existInGET('limit')? intval($request->getDataGET('limit'), 10) : 4;
        $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        
        if (!$this->productDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        /**
         * @var Product $product
         */
        $product = $this->productDAOManager->findById($id);
        if (!$this->productDAOManager->checkByCategory($product->getCategory()->getId(), $limit, $offset)) {
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_PRODUCT, $product);
        $request->addAttribute(self::ATT_PRODUCTS, $this->productDAOManager->findByCategory($product->getCategory()->getId(), $limit, $offset));
        $request->addAttribute(self::ATT_COUNT_PRODUCT, $this->productDAOManager->countByCategory($product->getCategory()->getId()));
        $this->itemMenuProduct($request);
    }
    
    /**
     * ajout/modification d'un produit
     * @param Request $request
     * @param Response $response
     */
    public function executeAddProduct (Request $request, Response $response) : void {
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new ProductValidator($this->getDaoManager());
            $product = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/products/{$product->getId()}/");
            }

            $request->addAttribute(self::ATT_PRODUCT, $product);
            $form->includeFeedback($request);
        }
        
        if (!$this->categoryDAOManager->hasData()) {//categories
            $response->sendRedirect("/admin/products/categories/add.html");
        }
        
        $request->addAttribute(self::ATT_CATEGORIES, $this->categoryDAOManager->findAll());
        $this->itemMenuOtherOperations($request);
        $request->addAttribute(self::ITEM_MENU_OTHER_OPTERATIONS, self::ITEM_MENU_ADD_PRODUCT);
    }
    
    /**
     * mise en jour d'un produit
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateProduct (Request $request, Response $response) : void {

        $id = intval($request->getDataGET("id"), 10);
        if (!$this->productDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        if ( $request->getMethod() == Request::HTTP_GET) {
            $product = $this->productDAOManager->findById($id);
        } else  {
            $form = new ProductValidator($this->getDaoManager());
            $product = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/products/{$product->getId()}/");
            }
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_CATEGORIES, $this->categoryDAOManager->findAll());
        $request->addAttribute(self::ATT_PRODUCT, $product);
        $this->itemMenuProduct($request);
    }
    
    /**
     * ajout d'un noveau stock pour un produit specifique
     * @param Request $request
     * @param Response $response
     */
    public function executeAddStock (Request $request, Response $response) : void {
        $id = intval($request->getDataGET("productId"), 10);//id du produit
        if (!$this->productDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $product = $this->productDAOManager->findById($id);
        $request->addAttribute(self::ATT_PRODUCT, $product);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new StockFormValidator($this->getDaoManager());
            $stock = $form->createAfterValidation($request);
            if(!$form->hasError()) {
                $response->sendRedirect("/admin/products/{$id}/stocks/");
            }
            $request->addAttribute(self::ATT_STOCK, $stock);
            $form->includeFeedback($request);
        }
    }
    
    /**
     * Visalisation de la liste des categories des produits
     * @param Request $request
     * @param Response $response
     */
    public function executeCategories (Request $request, Response $response) : void {
        
        if (!$this->categoryDAOManager->hasData()){
            $response->sendRedirect("{$request->getURI()}/add.html");
        }
        
        $categories = $this->categoryDAOManager->findAll();
        $request->addAttribute(self::ATT_CATEGORIES, $categories);
    }
    
    /**
     * enregistrement d'une nouvelle category
     * @param Request $request
     * @param Response $response
     */
    public function executeAddCategory (Request $request, Response $response) : void {
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new CategoryValidator($this->getDaoManager());
            $category = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/products/categories/");
            }
            
            $request->addAttribute(self::ATT_CATEGORY, $category);
            $form->includeFeedback($request);
        }
    }
    
    /**
     * Edition des informations d'une categirie
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateCategory (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        if ($request->getMethod() == Request::HTTP_GET) {
            if (!$this->categoryDAOManager->checkById($id)) {
                $response->sendError();
            }
            
            $category = $this->categoryDAOManager->findById($id);
        } else {
            $form = new CategoryValidator($this->getDaoManager());
            $category = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/products/categories/{$id}/");
            }
            $form->includeFeedback($request);
        } 
        
        $request->addAttribute(self::ATT_CATEGORY, $category);
    }
    
    
    /**
     * visualisation des stocks
     * @param Request $request
     * @param Response $response
     */
    public function executeStocks (Request $request, Response $response) : void {
        
        if (!$this->productDAOManager->hasData()) {
            $response->sendRedirect("/admin/products/");
        }
        
        $limit = $request->existInGET("limit")? intval($request->getDataGET("limit"), 10) : 10;
        $offset = $request->existInGET("offset")? intval($request->getDataGET("offset"), 10) : 0;
        
        if (!$this->stockDAOManager->checkAll($limit, $offset)) {
            $response->sendError();
        }
        
        $stocks = $this->stockDAOManager->findAll($limit, $offset);
        $request->addAttribute(self::ATT_STOCKS, $stocks);
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_STOCKS);
    }
}

