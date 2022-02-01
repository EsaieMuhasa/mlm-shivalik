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
            $products = $this->productDAOManager->findAll();
        } else {
            $products = [];
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
    }
}

