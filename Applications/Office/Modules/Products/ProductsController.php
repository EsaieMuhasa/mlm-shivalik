<?php
namespace Applications\Office\Modules\Products;

use PHPBackend\Http\HTTPController;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Filters\SessionOfficeFilter;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Managers\CategoryDAOManager;
use Core\Shivalik\Managers\CommandDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductsController extends HTTPController
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
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * @var CategoryDAOManager
     */
    private $categoryDAOManager;
    
    /**
     * @var CommandDAOManager
     */
    private $commandDAOManager;
    
    
    
    /**
     * @var Office
     */
    private $office;
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Http\HTTPController::init()
     */
    protected function init (Request $request, Response $response): void {
        $this->office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
        $request->addAttribute(self::ATT_VIEW_TITLE, "Product");
        $request->addAttribute(self::ATT_COUNT_PRODUCT, $this->productDAOManager->countVailableByOffice($this->office->getId()));
    }
    
    /**
     * Visualisation des produits disponible dans le stock auxilitaire
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        if ($this->productDAOManager->checkVailableByOffice($this->office->getId())) {
            $products = $this->productDAOManager->findVailableByOffice($this->office->getId());
        } else {
            $products = [];
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
    }
    
    /**
     * Visialisation de tout les produits au niveau du shop centrale
     * @param Request $request
     * @param Response $response
     */
    public function executeProducts (Request $request, Response $response) : void{
        
    }
}

