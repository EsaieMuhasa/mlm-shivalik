<?php

namespace Applications\Admin\Modules\Offices;

use Applications\Admin\AdminController;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\OfficeSize;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Managers\AuxiliaryStockDAOManager;
use Core\Shivalik\Managers\CommandDAOManager;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\OfficeAdminDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Managers\RaportWithdrawalDAOManager;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use Core\Shivalik\Managers\StockDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Validators\AuxiliaryStockFormValidator;
use Core\Shivalik\Validators\OfficeAdminFormValidator;
use Core\Shivalik\Validators\OfficeFormValidator;
use Core\Shivalik\Validators\OfficeSizeFormValidator;
use Core\Shivalik\Validators\VirtualMoneyFormValidator;
use Core\Shivalik\Validators\WithdrawalFormValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;

/**
 * @author Esaie MUHASA
 * Controlle des actions qui touches:
 * + les offices: les offices en tant que donne
 * + Les operations faitent dans un office (stocks, vente, afiliations, ...)      
 */
class OfficesController extends AdminController {
	
	//dynamisation des menues
	const ATT_ACTIVE_ITEM_MENU = 'OFFICE_ACTIVE_ITEM_MENU';
	const ATT_ITEM_MENU_DASHBOARD = 'OFFICE_ACTIVE_ITEM_MENU_DASHBOARD';
	const ATT_ITEM_MENU_MEMBERS = 'OFFICE_ACTIVE_ITEM_MENU_MEMBERS';
	const ATT_ITEM_MENU_HISTORY = 'OFFICE_ACTIVE_ITEM_MENU_HISTORY';
	const ATT_ITEM_MENU_OFFICE_ADMIN = 'OFFICE_ACTIVE_ITEM_MENU_OFFICE_ADMIN';
	const ATT_ITEM_MENU_VIRTUAL_MONEY = 'OFFICE_ACTIVE_ITEM_MENU_VIRTUAL_MONEY';
	const ATT_ITEM_MENU_WITHDRAWALS = 'OFFICE_ACTIVE_ITEM_MENU_WITHDRAWALS';
	const ATT_ITEM_MENU_STOCKS = 'OFFICE_ACTIVE_ITEM_MENU_STOCKS';
	//==
	
	const ATT_SIZES = 'sizes';
	const ATT_COUNTRYS = 'countrys';
	
	const ATT_OFFICE = 'office';
	const ATT_OFFICES = 'offices';
	const ATT_OFFICE_ADMIN = 'officeAdmin';
	
	const ATT_MEMBER = 'member';
	const ATT_MEMBERS = 'members';
	const ATT_GRADES_MEMBERS = 'gradesMembers';
	
	const ATT_OFFICE_SIZE = 'officeSize';
	const ATT_COUNT_MEMEBERS = 'COUNT_MEMBERS_IN_OFFICE';
	const ATT_WITHDRAWALS = 'WITHDRAWALS';
	const ATT_WITHDRAWALS_AMOUNT = 'WITHDRAWALS_AMOUNT';
	
	//pour les monais virtuels
	const ATT_VIRTUAL_MONEY = 'virtualMoney';
	const ATT_VIRTUAL_MONEYS = 'virtualMoneys';
	const ATT_REQUEST_VIRTUAL_MONEY = 'requestVirtualMoney';
	const ATT_RAPORTS_WITHDRAWALS = 'raportWithdrawals';
	const ATT_COUNT_WITHDRAWALS = 'count_withdrawals_occurences';//nombre d'occurence des retrait deja fait dans un  office
	
	const ATT_MONTH = 'MONTH';
	const CONFIG_MAX_MEMBER_VIEW_STEP = 'maxMembers';
	
	//stock
	const ATT_STOCK = 'stock';
	const ATT_STOCKS = 'stocks';
	//==
	
	/**
	 * @var CountryDAOManager
	 */
	private $countryDAOManager;
	
	/**
	 * @var SizeDAOManager
	 */
	private $sizeDAOManager;
	
	/**
	 * @var OfficeDAOManager
	 */
	private $officeDAOManager;
	
	/**
	 * @var OfficeSizeDAOManager
	 */
	private $officeSizeDAOManager;
	
	/**
	 * @var OfficeAdminDAOManager
	 */
	private $officeAdminDAOManager;
	
	/**
	 * @var GradeMemberDAOManager
	 */
	private $gradeMemberDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManager;
	
	/**
	 * @var RequestVirtualMoneyDAOManager
	 */
	private $requestVirtualMoneyDAOManager;
	
	/**
	 * @var RaportWithdrawalDAOManager
	 */
	private $raportWithdrawalDAOManager;
	
	/**
	 * @var StockDAOManager
	 */
	private $stockDAOManager;
	
	/**
	 * @var ProductDAOManager
	 */
	private $productDAOManager;
	
	/**
	 * @var AuxiliaryStockDAOManager
	 */
	private $auxiliaryStockDAOManager;
	
	/**
	 * @var CommandDAOManager
	 */
	private $commandDAOManager;
	
	/**
	 * dans le cas de la consultation de l'une des branches qui contiens les operations fait dans un office
	 * cette attribut est initialiser par la methode init.
	 * Il est null uniquement dans le cas ci-dessous:
	 * + visialisation de la liste des offices
	 * + creation d'un nouveau office
	 * @var Office
	 */
	private $office;
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Admin\AdminController::__construct()
	 */
	public function __construct(Application $application, string $module, string $action) {
		parent::__construct($application, $module, $action);
		$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Offices");
		$this->init($application->getRequest(), $application->getResponse());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PHPBackend\Http\HTTPController::init()
	 */
	protected function init (Request $request, Response $response) : void {
		if ($request->existInGET('id')) {
			$id = intval($request->getDataGET('id'), 10);//l'identifiant de l'office
			
			if (!$this->officeDAOManager->checkById($id)) {
				$response->sendError();
			}
			
			$office = $this->officeDAOManager->findById($id);
			if ($office->isCentral()) {
			    $response->sendError("No matched ressource at this URL: {$request->getURI()}");
			}
			$request->addAttribute(self::ATT_OFFICE, $office);
			
			$this->office = $office;
			if ($this->officeAdminDAOManager->checkByOffice($id) || $this->officeAdminDAOManager->checkByOffice($id, false)) {
				$admin = $this->officeAdminDAOManager->findAdminByOffice($id);
				$request->addAttribute(self::ATT_OFFICE_ADMIN, $admin);
			}
			
		}
	}
	
	
	/**
	 * visualisation des la liste des bureau qui existe dans le systeme
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeIndex (Request $request, Response $response) : void {
		if ($this->officeDAOManager->countAll()>0) {
			$offices = $this->officeDAOManager->findAll();
		}else {
			$offices = array();
		}
		
		/**
		 * @var OfficeSize $office
		 */
		foreach ($offices as $office) {
		    if ($this->officeSizeDAOManager->checkByOffice($office->getId())) {
    		    $office->setOfficeSize($this->officeSizeDAOManager->findCurrentByOffice($office->getId()));
		    }
		}
		
		$request->addAttribute(self::ATT_OFFICES, $offices);
	}
	
	/**
	 * creation d'un nouveau bureau secodaire dans le systeme
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeAddOffice (Request $request, Response $response) : void {
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new OfficeSizeFormValidator($this->getDaoManager());
			
			$os = $form->createAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/admin/offices/");
			}
			
			$request->addAttribute(self::ATT_OFFICE_SIZE, $os);
			$request->addAttribute(self::ATT_OFFICE, $os->getOffice());
			$form->includeFeedback($request);
		}
		
		$request->addAttribute(self::ATT_SIZES, $this->sizeDAOManager->findAll());
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
	}
	
	/**
	 * Modification de la visibilite d'un office
	 * cette action n'ast pas de vue,
	 * apre execution d'operation, une demande de redirection est renvoyer en repose
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeOfficeVisibility (Request $request, Response $response) : void {
	    $visible = $request->getDataGET('option') == 'visible';
	    $this->officeDAOManager->updateVisibility($this->office->getId(), $visible);
	    $response->sendRedirect("/admin/offices/table.{$request->getExtensionURI()}");
	}
	
	/**
	 * modification des information d'un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeUpdateOffice (Request $request, Response $response) : void {
		
		$request->addAttribute('localisation', $this->office->getLocalisation());
		$id = $this->office->getId();
		
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new OfficeFormValidator($this->getDaoManager());
			$request->addAttribute($form::CHAMP_ID, $id);
			$office = $form->updateAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/admin/offices/{$id}/");
			}
			
			$request->addAttribute(self::ATT_OFFICE, $office);
			$form->includeFeedback($request);
		}
		
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
	}
	
	/**
	 * Visualisation/ou creation d'un administrateur pour un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeOfficeAdmin (Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_OFFICE_ADMIN);
		if ($request->existInGET('adminId')) {
			$id = $request->getDataGET('adminId');
			$option = $request->getDataGET('option');
			
			if (!$this->officeAdminDAOManager->checkById($id)) {
				$response->sendError();
			}
			
			$admin = $this->officeAdminDAOManager->findById($id);
			if ($admin->getOffice()->getId() != $this->office->getId()) {
				$response->sendError();
			}
			
			$this->officeAdminDAOManager->updateState($id, $option == 'enable');
			$response->sendRedirect("/admin/offices/{$this->office->getId()}/");
		}
		
	}
	
	/**
	 * Creation de l'administrateur de l'office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeCreateOfficeAdmin (Request $request, Response $response) : void {
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new OfficeAdminFormValidator($this->getDaoManager());
			$request->addAttribute($form::FIELD_OFFICE, $this->office);
			$admin = $form->createAfterValidation($request);
			
			if (!$form->hasError()){
				$response->sendRedirect("/admin/offices/{$this->office->getId()}/admin.html");
			}
			
			$form->includeFeedback($request);
			$request->addAttribute(self::ATT_OFFICE_ADMIN, $admin);
		}
		
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
	}
	
	/**
	 * Initialisation du mot de passe d'un administrateur d'un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeResetPassword (Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_OFFICE_ADMIN);
		if ($request->getMethod() == Request::HTTP_POST) {
			$id = intval($request->getDataGET('adminId'), 10);
			
			$form = new OfficeAdminFormValidator($this->getDaoManager());
			$request->addAttribute($form::CHAMP_ID, $id);
			$form->updatePasswordAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/admin/offices/{$this->office->getId()}/");
			}
			
			$form->includeFeedback($request);
		}
	}
	
	/**
	 * le tableau de bord d'un bureau
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeDashboard (Request $request, Response $response) : void{
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_DASHBOARD);
		$nombreMembre = $this->memberDAOManager->countByOffice($this->office->getId());
		
		$this->office = $this->officeDAOManager->load($this->office);
		
		if ($this->requestVirtualMoneyDAOManager->checkWaiting($this->office->getId())) {
		    $requests = $this->requestVirtualMoneyDAOManager->findWaiting($this->office->getId());
		}else {
		    $requests = array();
		}
		
		if ($this->withdrawalDAOManager->checkByOffice($this->office->getId(), null, false)) {
		    $serveds = $this->withdrawalDAOManager->findByOffice($this->office->getId(), null, false);
		    $request->addAttribute(self::ATT_WITHDRAWALS, $serveds);
		}else {
		    $request->addAttribute(self::ATT_WITHDRAWALS, array());
		}
		
		$request->addAttribute(self::ATT_VIRTUAL_MONEYS, $requests);
		$request->addAttribute(self::ATT_COUNT_MEMEBERS, $nombreMembre);
	}
	
	/**
	 * visualisation des operations de retraits effectuer dans un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeWithdrawals (Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_WITHDRAWALS);
	    
	    $limit = $request->existInGET('limit')? intval($request->getDataGET('limit'), 10) : intval($request->getApplication()->getConfig()->get('defaultLimit')->getValue(), 10);
	    $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
	    
	    $this->office = $this->officeDAOManager->load($this->office);
	    $count = $this->withdrawalDAOManager->countByOffice($this->office->getId(), null, null);
	    
	    if ($this->withdrawalDAOManager->checkByOffice($this->office->getId(), null, null, $limit, $offset)) {
	        $serveds = $this->withdrawalDAOManager->findByOffice($this->office->getId(), null, null, $limit, $offset);
	        $request->addAttribute(self::ATT_WITHDRAWALS, $serveds);
	    } else {
	        $request->addAttribute(self::ATT_WITHDRAWALS, []);
	    }
	    
	    $offices = $this->officeDAOManager->findAll();
	    $request->addAttribute(self::ATT_OFFICES, $offices);
	    $request->addAttribute(self::ATT_COUNT_WITHDRAWALS, $count);
	}
	
	/**
	 * Redirection de l'argent, dans un autre office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeRedirectWithdrawal (Request $request, Response $response) : void {
	    
	    if ($request->getMethod() != Request::HTTP_POST) {
	        $response->sendRedirect("/admin/offices/");
	    }
	    
	    $id = intval($request->getDataGET('withdrawalId'), 10);
	    $office = intval($request->getDataGET('id'), 10);
	    
	    if (!$this->withdrawalDAOManager->checkById($id) || !$this->officeDAOManager->checkById($office)) {
	        $response->sendError("no data match at request URL");
	    }
	    
	    $withdrawal = $this->withdrawalDAOManager->findById($id);
	    if ($withdrawal->getAdmin() != null) {
	        $response->sendError("no data match at request URL");
	    }
	    
	    
        $form = new WithdrawalFormValidator($this->getDaoManager());
        $request->addAttribute($form::CHAMP_ID, $id);
        $request->addAttribute($form::FIELD_OFFICE, $office);
        $form->redirectAfterValidation($request);
        
        $request->addToast($form->buildAppMessage());
        $response->sendRedirect("/admin/offices/{$office}/withdrawals/");
	}
	
	/**
	 * tableau de board des monais virtuels
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeVirtualmoney(Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_VIRTUAL_MONEY);
		
		if ($this->gradeMemberDAOManager->checkByOffice($this->office->getId())) {
			$this->office->setOperations($this->gradeMemberDAOManager->findByOffice($this->office->getId()));
		}
		
		if ($this->virtualMoneyDAOManager->checkByOffice($this->office->getId())) {
		    $this->office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($this->office->getId()));
		}
		
		if ($this->requestVirtualMoneyDAOManager->checkWaiting($this->office->getId())) {
		    $requests = $this->requestVirtualMoneyDAOManager->findWaiting($this->office->getId());
		}else {
		    $requests = array();
		}
		
		$request->addAttribute(self::ATT_VIRTUAL_MONEYS, $requests);
	}
	
	/**
	 * envoie d'un nouveau forfait
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeSendVirtualMoney (Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_VIRTUAL_MONEY);
		
	    $virtual = new VirtualMoney();
		
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new VirtualMoneyFormValidator($this->getDaoManager());
			$request->addAttribute($form::FIELD_OFFICE, $this->office);
			$virtual = $form->createAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/admin/offices/{$this->office->getId()}/virtualmoney/");
			}
			$form->includeFeedback($request);
		}
		
		$request->addAttribute(self::ATT_VIRTUAL_MONEY, $virtual);
	}
	
	/**
	 * affichage de membres qui se sont adherer, en passant par le bureau
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeMembers (Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_MEMBERS);
	    
	    $nombre = $this->memberDAOManager->countByOffice($this->office->getId());
	    
	    if ($nombre>0) {
	        if ($request->existInGET('limit')) {
	            $offset = intval($request->getDataGET('offset'), 10);
	            $limit = intval($request->getDataGET('limit'), 10);
	        } else {
	            $limit = intval($request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)!=null? $request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)->getValue() : 50);
	            $offset = 0;
	        }
	        $members = $this->memberDAOManager->findByOffice($this->office->getId(), $limit, $offset);
	    }else {
	        $members = array();
	    }
	    
	    $request->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
	    $request->addAttribute(self::ATT_MEMBERS, $members);
	}
	
	/**
	 * Visualisation des comptes qui ont upgrader leurs compte dans un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeUpgrades (Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_MEMBERS);
	    //upgrades
	    if ($this->gradeMemberDAOManager->checkByOffice($this->office->getId(), true)) {
	        $packets = $this->gradeMemberDAOManager->findByOffice($this->office->getId(), true);
	    }else {
	        $packets = array();
	    }
	    
	    $request->addAttribute(self::ATT_GRADES_MEMBERS, $packets);
	}
	
	/**
	 * consultation de l'istorique des activites d'un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeHistory (Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_HISTORY);
	    
	    $date = null;
	    
	    if ($request->existInGET('date')) {
	        $date = new \DateTime($request->getDataGET('date'));
	        $month = new Month(intval($date->format('m'), 10), intval($date->format('Y'), 10));
	        $month->addSelectedDate($date);
	    }elseif ($request->existInGET('month')) {
	        $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
	    }else {	        
    	    $month = new Month();
	    }
	    $month->setLocal(Month::LOCAL_EN);
	    
	    $dateMin = ($date!=null? $date : $month->getFirstDay());
	    $dateMax = ($date!=null? $date : $month->getLastDay());
	    
	    //adhesion
	    if ($this->memberDAOManager->checkCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax)) {
	        $members = $this->memberDAOManager->findCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax);
	    }else {
	        $members = array();
	    }
	    
	    //Monais virtuel
	    if ($this->virtualMoneyDAOManager->checkCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax)) {
	        $virtuals = $this->virtualMoneyDAOManager->findCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax);
	    }else {
	        $virtuals = array();
	    }

	    
	    //upgrades
	    if ($this->gradeMemberDAOManager->checkUpgradeHistory($dateMin, $dateMax,$this->office->getId())) {
	        $packets = $this->gradeMemberDAOManager->findUpgradeHistory($dateMin, $dateMax, $this->office->getId());
	    }else {
	        $packets = array();
	    }
	    
	    //retraits
	    if ($this->withdrawalDAOManager->checkCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax)) {
	        $withdrawals = $this->withdrawalDAOManager->findCreationHistoryByOffice($this->office->getId(), $dateMin, $dateMax);
	    }else {
	        $withdrawals = array();
	    }
	    
	    //raports
	    if ($this->raportWithdrawalDAOManager->checkRaportInInterval($dateMin, $dateMax, $this->office->getId())) {
	        $raports = $this->raportWithdrawalDAOManager->findRaportInInterval($dateMin, $dateMax, $this->office->getId());
	        foreach ($raports as $raport) {
	            $raport->setWithdrawals($this->withdrawalDAOManager->findByRapport($raport->getId()));
	        }
	    } else {
	        $raports = array();
	    }
	    
	    $request->addAttribute(self::ATT_MONTH, $month);
	    $request->addAttribute(self::ATT_MEMBERS, $members);
	    $request->addAttribute(self::ATT_VIRTUAL_MONEYS, $virtuals);
	    $request->addAttribute(self::ATT_GRADES_MEMBERS, $packets);
	    $request->addAttribute(self::ATT_WITHDRAWALS, $withdrawals);
	    $request->addAttribute(self::ATT_RAPORTS_WITHDRAWALS, $raports);
	}
	
	//gestion de stock
	//==   ====    ===
	
	/**
	 * consultation des stocks d'un office
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeStocks(Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_STOCKS);
	    
	    if ($this->auxiliaryStockDAOManager->checkByOffice($this->office->getId())) {
	        $stocks = $this->auxiliaryStockDAOManager->loadByOffice($this->office->getId());
	    } else {
	        $stocks = [];
	    }
	    
	    $request->addAttribute(self::ATT_STOCKS, $stocks);
	}
	
	/**
	 * Creation d'un nouveau stock auxiliare
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeAddStock(Request $request, Response $response) : void{
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_STOCKS);
	    
	    if (!$this->stockDAOManager->checkByStatus(false)) {
	        $response->sendError("Unable to perform this operation because all stocks are empty");
	    }
	    
	    if ($request->getMethod() == Request::HTTP_POST) {
	        $form = new AuxiliaryStockFormValidator($this->getDaoManager());
	        $request->addAttribute($form::FIELD_OFFICE, $this->office);
	        $stock = $form->createAfterValidation($request);
	        
	        if (!$form->hasError()) {
        	    $response->sendRedirect("/admin/offices/{$this->office->getId()}/stocks/");
	        }
	        
	        $request->addAttribute(self::ATT_STOCK, $stock);
	        $form->includeFeedback($request);
	    }
	    
	    $request->addAttribute(self::ATT_STOCKS, $this->stockDAOManager->findByStatus(false));
	}
	
	/**
	 * Mis en jour d'un stock auxiliaire
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeUpdateStock(Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_STOCKS);
	    
	    $response->sendRedirect("/admin/offices/{$this->office->getId()}/stocks/");
	}
	
	/**
	 * Supression d'un stock auxiliaire
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeRemoveStock(Request $request, Response $response) : void{
	    
	    
	    $response->sendRedirect("/admin/offices/{$this->office->getId()}/stocks/");
	}
	//==
	//==

}

