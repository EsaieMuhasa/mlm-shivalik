<?php
namespace Applications\Common\Modules\Index;

use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\ProductDAOManager;

/**
 * Cotrolleur des pages indexs du site
 * -index
 * -about
 * -contact
 * -...
 * @author Esaie MUHASA
 *        
 */
class IndexController extends HTTPController
{
    
    const ATT_PRODUCTS  = 'products';
    
    //les menus
    const ACTIVE_ITEM_MENU = 'ACTIVE_COMMON_ITEM_MENU';
    const ITEM_MENU_HOME = 'ITEM_MENU_HOME';
    const ITEM_MENU_PRODUCT = 'ITEM_MENU_PRODUCT';
    const ITEM_MENU_ABOUT = 'ITEM_MENU_ABOUT';
    const ITEM_MENU_CONTACT = 'ITEM_MENU_CONTACT';
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * index generale du site
     * @param Request $reqest
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        
        if ($this->productDAOManager->hasData()) {
            $products = $this->productDAOManager->findAll(12);
        } else {
            $products = [];
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
        $request->addAttribute(self::ACTIVE_ITEM_MENU, self::ITEM_MENU_HOME);
    }
    
    /**
     * Consultation des produits
     * @param Request $request
     * @param Response $response
     */
    public function executeProducts (Request $request, Response $response) : void {
        
        $limit = $request->existInGET('limit')? intval($request->getDataGET('limit'), 10) : 12;
        $offset =  $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        
        if ($this->productDAOManager->checkAll($limit, $offset)) {
            $products = $this->productDAOManager->findAll($limit, $offset);
        } else {
            if ($offset == 0) {
                $response->sendRedirect("/");
            } 
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
        $request->addAttribute(self::ACTIVE_ITEM_MENU, self::ITEM_MENU_PRODUCT);
    }
    
    /**
     * Affichage de la page d'apropos de shivalik
     * @param Request $request
     */
    public function executeAbout (Request $request) : void {
        
        $request->addAttribute(self::ACTIVE_ITEM_MENU, self::ITEM_MENU_ABOUT);
    }
    
    /**
     * page de contacte
     * @param Request $request
     * @param Response $response
     */
    public function executeContact (Request $request, Response $response) : void {
        $request->addAttribute(self::ACTIVE_ITEM_MENU, self::ITEM_MENU_CONTACT);
    }
}

