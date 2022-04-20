<?php
namespace Applications\Office\Modules\Products;

use Core\Shivalik\Entities\Command;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;
use Core\Shivalik\Managers\CategoryDAOManager;
use Core\Shivalik\Managers\CommandDAOManager;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Validators\MemberFormValidator;
use Core\Shivalik\Validators\ProductOrderedFormValidator;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;
use PHPBackend\Calendar\Year;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Entities\Product;

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
    
    const ATT_MONTH = 'SELECTED_MONTH';
    const ATT_YEAR = 'SELECTED_YEAR';
    
    //activation/desactivation des menus
    const ATT_ACTIVE_MENU = 'PRODUCT_ACTIVE_ITEM_MENU';
    const ITEM_MENU_DASHBOARD = 'ITEM_MENU_DASBOARD';
    const ITEM_MENU_PRODUCTS = 'ITEM_MENU_PRODUCT';
    const ITEM_MENU_STOCKS = 'ITEM_MENU_STOCK';
    const ITEM_MENU_COMMAND = 'ITEM_MENU_COMMAND';
    const ITEM_MENU_ADD_PRODUCT = 'ITEM_MENU_ADD_PRODUCT';
    
    const ATT_COMMAND = 'command';
    const ATT_MEMBER = 'member';
    
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
     * @var AuxiliaryStockDAOManager
     */
    private $auxiliaryStockDAOManager;
    
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
     * Visualisation des ventes, conformement au calendar
     * Il est possible de visualiser le operations faites:
     * + pour une date
     * + pour un mois
     * + pour une semaine
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_DASHBOARD);
        
        $limit = $request->existInGET('limit')? intval($request->getDataGET('limit'), 10) : 5;
        $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        $title = '';
        
        if ($request->existInGET('firstDay')) {//selection des operations faite dans une semaine
            $firstDay = new \DateTime($request->getDataGET('firstDay'));
            $lastDay = new \DateTime($request->getDataGET('lastDay'));
            $week = intval($request->getDataGET('week'), 10);
            $month = new Month(intval($firstDay->format('m'), 10), intval($firstDay->format('Y'), 10));
            $month->addSelectedWeek($week);
            $title = 'Commands of '.($week+1).'<sup>th</sup> week, of '.$month;
        } else if($request->existInGET('month')){//selection des commandes faites dans un mois
            $monthIndex = intval($request->getDataGET('month'), 10);
            if($monthIndex > 12){
                $response->sendError();
            }
            $yearIndex = intval($request->getDataGET('year'), 10);
            $month = new Month($monthIndex, $yearIndex);
            $title = 'Commands of '.$month;
        } else  {//selection des operations faite en une date
            $date = new \DateTime($request->getDataGET('date'));
            $month = new Month(intval($date->format('m'), 10), intval($date->format('Y'), 10));
            $month->addSelectedDate($date);
            $title = 'Commands of '.$date->format('d').' '.$month;
        }
        
        $year = new Year($month->getYear());
        $year->addSelectedMonth($month->getMonth());
        
        $request->addAttribute(self::ATT_YEAR, $year);
        $request->addAttribute(self::ATT_MONTH, $month);
        $request->addAttribute('title', $title);
    }
    
    /**
     * Visialisation de tout les produits au niveau du shop centrale
     * @param Request $request
     * @param Response $response
     */
    public function executeProducts (Request $request, Response $response) : void{
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_PRODUCTS);
        if ($this->productDAOManager->checkVailableByOffice($this->office->getId())) {
            /**
             * @var Product[] $products
             */
            $products = $this->productDAOManager->findVailableByOffice($this->office->getId());
        } else {
            $products = [];
        }
        
        foreach ($products as $product) {
            $product->setStocks($this->auxiliaryStockDAOManager->findByProductInOffice($product->getId(), $this->office->getId(), false));
        }
        
        $request->addAttribute(self::ATT_PRODUCTS, $products);
    }
    
    /**
     * Pour effectuer une commande
     * @param Request $request
     * @param Response $response
     */
    public function executeCommand (Request $request, Response $response) : void {
        if(!$request->getSession()->hasAttribute(self::ATT_COMMAND)) {
            $response->sendRedirect("/office/products/command/member.html");
        }
        
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_COMMAND);
    }
    
    /**
     * Pour annuler la commande encours
     * @param Request $request
     * @param Response $response
     */
    public function executeCancelCommand (Request $request, Response $response) : void {
        $request->getSession()->removeAttribute(self::ATT_COMMAND);
        $response->sendRedirect("/office/products/");
    }
    
    
    /**
     * Validation definitive de la commande
     * @param Request $request
     * @param Response $response
     */
    public function executeValidateCommand (Request $request, Response $response) : void{
        
    }
    
    /**
     * Selection du membre qui doit effectuer la commande
     * @param Request $request
     * @param Response $response
     */
    public function executeMemberCommand (Request $request, Response $response) : void {
        if($request->getMethod() == Request::HTTP_POST) {
            $form = new MemberFormValidator($this->getDaoManager());
            $member = $form->searchByIdAfterValidation($request);
            
            if(!$form->hasError()) {
                $command = $request->getSession()->getAttribute(self::ATT_COMMAND);
                
                if($command == null) {
                    $command = new Command();
                    $command->setOffice($this->office);
                }
                
                $command->setMember($member);
                if(!$request->getSession()->hasAttribute(self::ATT_COMMAND)) {
                    $request->getSession()->addAttribute(self::ATT_COMMAND, $command);
                }
                
                $response->sendRedirect("/office/products/command/product.html");
            }
            
            $request->addAttribute(self::ATT_MEMBER, $member);
        }
        
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_COMMAND);
    }
    
    /**
     * Selection de produits pour une commande
     * @param Request $request
     * @param Response $response
     */
    public function executeProductCommand (Request $request, Response $response) : void {
        if (!$this->auxiliaryStockDAOManager->checkByOffice($this->office->getId(), false)) {
            $response->sendRedirect("/office/products/");
        }
        
        /**
         * @var Command $command
         */
        $command = $request->getSession()->getAttribute(self::ATT_COMMAND);
        
        if($request->getMethod() == Request::HTTP_POST) {
            $form = new ProductOrderedFormValidator($this->getDaoManager());
            $products = $form->prepareCommand($request);
            
            if(!$form->hasError()) {
                $command->setProducts($products);
                $response->sendRedirect("/office/products/command/");
            }
            
            $form->includeFeedback($request);
        }
        
        $stocks = $this->auxiliaryStockDAOManager->loadByOffice($this->office->getId(), false);
        $request->addAttribute(self::ATT_STOCKS, $stocks);
        
        
        $request->addAttribute(self::ATT_ACTIVE_MENU, self::ITEM_MENU_COMMAND);
    }
    
}

