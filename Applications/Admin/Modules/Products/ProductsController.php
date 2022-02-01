<?php
namespace Applications\Admin\Modules\Products;

use Applications\Admin\AdminController;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Validators\ProductValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductsController extends AdminController
{
    const ATT_PRODUCT ='product';
    const ATT_PRODUCTS ='products';
    const ATT_COUNT_PRODUCT = 'count_products';
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    
    /**
     * {@inheritDoc}
     * @see \Applications\Admin\AdminController::__construct()
     */
    public function __construct(Application $application, string $action, string $module)
    {
        parent::__construct($application, $action, $module);
        $application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Products");
    }

    /**
     * Affichage des produits
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        
        $request->addAttribute(self::ATT_PRODUCTS, []);
        $request->addAttribute(self::ATT_COUNT_PRODUCT, 0);
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
                $response->sendRedirect("/admin/products/{$product->getId()}");
            }
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_PRODUCT, $product);
        
    }
}

