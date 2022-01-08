<?php

namespace Applications\Admin\Modules\Offices;

use Applications\Admin\AdminController;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\OfficeAdminDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\RaportWithdrawalDAOManager;
use Core\Shivalik\Entities\Office;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;
use Core\Shivalik\Entities\OfficeSize;
use Core\Shivalik\Validators\OfficeSizeFormValidator;
use Core\Shivalik\Validators\OfficeFormValidator;
use Core\Shivalik\Validators\OfficeAdminFormValidator;
use Core\Shivalik\Validators\WithdrawalFormValidator;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Validators\VirtualMoneyFormValidator;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class OfficesController extends AdminController {
	
	//dynamisation des menues
	const ATT_ACTIVE_ITEM_MENU = 'OFFICE_ACTIVE_ITEM_MENU';
	const ATT_ITEM_MENU_DASHBOARD = 'OFFICE_ACTIVE_ITEM_MENU_DASHBOARD';
	const ATT_ITEM_MENU_MEMBERS = 'OFFICE_ACTIVE_ITEM_MENU_MEMBERS';
	const ATT_ITEM_MENU_HISTORY = 'OFFICE_ACTIVE_ITEM_MENU_HISTORY';
	const ATT_ITEM_MENU_OFFICE_ADMIN = 'OFFICE_ACTIVE_ITEM_MENU_OFFICE_ADMIN';
	const ATT_ITEM_MENU_VIRTUAL_MONEY = 'OFFICE_ACTIVE_ITEM_MENU_VIRTUAL_MONEY';
	
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
	
	const ATT_MONTH = 'MONTH';
	const CONFIG_MAX_MEMBER_VIEW_STEP = 'maxMembers';
	
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
	 * @var Office
	 */
	private $office;
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Admin\AdminController::__construct()
	 */
	public function __construct(Application $application, string $action, string $module) {
		parent::__construct($application, $action, $module);
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Offices");
		$this->init($application->getHttpRequest(), $application->getHttpResponse());
	}
	
	/**
	 * chargement des donnes obligatorie
	 * lors d ela consultation d'un office, on chage les donnees qui le concerne 
	 * @param Request $request
	 * @param Response $response
	 */
	private function init (Request $request, Response $response) : void {
		if ($request->existGET('id')) {
			$id = intval($request->getDataGET('id'), 10);
			
			if (!$this->officeDAOManager->idExist($id)) {
				$response->sendError();
			}
			
			$office = $this->officeDAOManager->getForId($id);
			$request->addAttribute(self::ATT_OFFICE, $office);
			
			$this->office = $office;
			if ($this->officeAdminDAOManager->hasAdmin($id) || $this->officeAdminDAOManager->hasAdmin($id, false)) {
				$admin = $this->officeAdminDAOManager->getAdmin($id);
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
			$offices = $this->officeDAOManager->getAll();
		}else {
			$offices = array();
		}
		
		/**
		 * @var OfficeSize $office
		 */
		foreach ($offices as $office) {
		    if ($this->officeSizeDAOManager->hasSize($office->getId())) {
    		    $office->setOfficeSize($this->officeSizeDAOManager->getCurrent($office->getId()));
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
		
		$request->addAttribute(self::ATT_SIZES, $this->sizeDAOManager->getAll());
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
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
				$response->sendRedirect("/admin/offices/");
			}
			
			$request->addAttribute(self::ATT_OFFICE, $office);
			$form->includeFeedback($request);
		}
		
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
	}
	
	/**
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeOfficeAdmin (Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_OFFICE_ADMIN);
		if ($request->existGET('adminId')) {
			$id = $request->getDataGET('adminId');
			$option = $request->getDataGET('option');
			
			if (!$this->officeAdminDAOManager->idExist($id)) {
				$response->sendError();
			}
			
			$admin = $this->officeAdminDAOManager->getForId($id);
			if ($admin->getOffice()->getId() != $this->office->getId()) {
				$response->sendError();
			}
			
			$this->officeAdminDAOManager->updateState($id, $option == 'enable');
			$response->sendRedirect("/admin/offices/{$this->office->getId()}/");
		}
		
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new OfficeAdminFormValidator($this->getDaoManager());
			$request->addAttribute($form::FIELD_OFFICE, $this->office);
			$admin = $form->createAfterValidation($request);
			
			if (!$form->hasError()){
				$response->sendRedirect("/admin/offices/{$this->office->getId()}/");
			}
			
			$form->includeFeedback($request);
			$request->addAttribute(self::ATT_OFFICE_ADMIN, $admin);
		}
		
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
	}
	
	/**
	 * 
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
	 * le tableau de bord dans un bureau
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeDashboard (Request $request, Response $response) : void{
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_DASHBOARD);
		$nombreMembre = $this->memberDAOManager->countCreatedBy($this->office->getId());
		
		$this->office = $this->officeDAOManager->load($this->office);
		
		if ($this->requestVirtualMoneyDAOManager->hasWaiting($this->office->getId())) {
		    $requests = $this->requestVirtualMoneyDAOManager->getWaiting($this->office->getId());
		}else {
		    $requests = array();
		}
		
		if ($this->withdrawalDAOManager->hasRequest($this->office->getId(), null, false)) {
		    $serveds = $this->withdrawalDAOManager->getOfficeRequests($this->office->getId(), null, false);
		    $request->addAttribute(self::ATT_WITHDRAWALS, $serveds);
		}else {
		    $request->addAttribute(self::ATT_WITHDRAWALS, array());
		}
		
		$request->addAttribute(self::ATT_VIRTUAL_MONEYS, $requests);
		$request->addAttribute(self::ATT_COUNT_MEMEBERS, $nombreMembre);
		
		
		$offices = $this->officeDAOManager->getAll();
		$request->addAttribute(self::ATT_OFFICES, $offices);
	}
	
	/**
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeRedirectWithdrawal (Request $request, Response $response) : void {
	    
	    if ($request->getMethod() != Request::HTTP_POST) {
	        $response->sendRedirect("/admin/offices/");
	    }
	    
	    $id = intval($request->getDataGET('withdrawalId'), 10);
	    $office = intval($request->getDataGET('id'), 10);
	    
	    if (!$this->withdrawalDAOManager->idExist($id) || !$this->officeDAOManager->idExist($office)) {
	        $response->sendError("no data match at request URL");
	    }
	    
	    $withdrawal = $this->withdrawalDAOManager->getForId($id);
	    if ($withdrawal->getAdmin() != null) {
	        $response->sendError("no data match at request URL");
	    }
	    
	    
        $form = new WithdrawalFormValidator($this->getDaoManager());
        $request->addAttribute($form::CHAMP_ID, $id);
        $request->addAttribute($form::FIELD_OFFICE, $office);
        $form->redirectAfterValidation($request);
        
        $request->addAppMessage($form->buildAppMessage());
        $response->sendRedirect("/admin/offices/{$office}/");
	}
	
	/**
	 * tableau de board des monais virtuels
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeVirtualmoney(Request $request, Response $response) : void {
		$request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_VIRTUAL_MONEY);
		
		if ($this->gradeMemberDAOManager->hasOperation($this->office->getId())) {
			$this->office->setOperations($this->gradeMemberDAOManager->getOperations($this->office->getId()));
		}
		
		if ($this->virtualMoneyDAOManager->hasVirtualMoney($this->office->getId())) {
			$this->office->setVirtualMoneys($this->virtualMoneyDAOManager->forOffice($this->office->getId()));
		}
		
		if ($this->requestVirtualMoneyDAOManager->hasWaiting($this->office->getId())) {
		    $requests = $this->requestVirtualMoneyDAOManager->getWaiting($this->office->getId());
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
		
	    $money = new RequestVirtualMoney();
	    $virtual = new VirtualMoney();
	    
		if ($request->existGET('request')) {
		    $id = intval($request->getDataGET('request'), 10);
		    if (!$this->requestVirtualMoneyDAOManager->idExist($id)) {
		        $response->sendError();
		    }
		    
		    $requestMoney = $this->requestVirtualMoneyDAOManager->getForId($id);
		    if ($requestMoney->getOffice()->getId() != $this->office->getId()) {
		        $response->sendError();
		    }
		    
		    $money->setAmount($requestMoney->getAmount());
		    $request->addAttribute(VirtualMoneyFormValidator::FIELD_REQUEST_MONEY, $requestMoney);
		}
		
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new VirtualMoneyFormValidator($this->getDaoManager());
			$request->addAttribute($form::FIELD_OFFICE, $this->office);
			$virtual = $form->createAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/admin/offices/{$this->office->getId()}/virtualmoney/");
			}
			$form->includeFeedback($request);
		}
		
		if ($this->gradeMemberDAOManager->hasOperation($this->office->getId(), null, false)) {//Recuperation de toutes les operations 
		    //qui ne sont pas encore payer
		    $virtual->setDebts($this->gradeMemberDAOManager->getOperations($this->office->getId(), null, false));
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
	    
	    $nombre = $this->memberDAOManager->countCreatedBy($this->office->getId());
	    
	    if ($nombre>0) {
	        if ($request->existGET('limit')) {
	            $offset = intval($request->getDataGET('offset'), 10);
	            $limit = intval($request->getDataGET('limit'), 10);
	        } else {
	            $limit = intval($request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)!=null? $request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)->getValue() : 50);
	            $offset = 0;
	        }
	        $members = $this->memberDAOManager->getCreatedBy($this->office->getId(), $limit, $offset);
	    }else {
	        $members = array();
	    }
	    
	    $request->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
	    $request->addAttribute(self::ATT_MEMBERS, $members);
	}
	
	/**
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeUpgrades (Request $request, Response $response) : void {
	    $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_MEMBERS);
	    //upgrades
	    if ($this->gradeMemberDAOManager->hasOperation($this->office->getId(), true)) {
	        $packets = $this->gradeMemberDAOManager->getOperations($this->office->getId(), true);
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
	    
	    if ($request->existGET('date')) {
	        $date = new \DateTime($request->getDataGET('date'));
	        $month = new Month(intval($date->format('m'), 10), intval($date->format('Y'), 10));
	        $month->addSelectedDate($date);
	    }elseif ($request->existGET('month')) {
	        $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
	    }else {	        
    	    $month = new Month();
	    }
	    $month->setLocal(Month::LOCAL_EN);
	    
	    $dateMin = ($date!=null? $date : $month->getFirstDay());
	    $dateMax = ($date!=null? $date : $month->getLastDay());
	    
	    //adhesion
	    if ($this->memberDAOManager->hasCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()))) {
	        $members = $this->memberDAOManager->getCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()));
	    }else {
	        $members = array();
	    }
	    
	    //Monais virtuel
	    if ($this->virtualMoneyDAOManager->hasCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()))) {
	        $virtuals = $this->virtualMoneyDAOManager->getCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()));
	    }else {
	        $virtuals = array();
	    }

	    
	    //upgrades
	    if ($this->gradeMemberDAOManager->hasUpgradeHistory($dateMin, $dateMax, array('office' => $this->office->getId()))) {
	        $packets = $this->gradeMemberDAOManager->getUpgradeHistory($dateMin, $dateMax, array('office' => $this->office->getId()));
	    }else {
	        $packets = array();
	    }
	    
	    //retraits
	    if ($this->withdrawalDAOManager->hasCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()))) {
	        $withdrawals = $this->withdrawalDAOManager->getCreationHistory($dateMin, $dateMax, array('office' => $this->office->getId()));
	    }else {
	        $withdrawals = array();
	    }
	    
	    //raports
	    if ($this->raportWithdrawalDAOManager->hasRaportInInterval($dateMin, $dateMax, $this->office->getId())) {
	        $raports = $this->raportWithdrawalDAOManager->getRaportInInterval($dateMin, $dateMax, $this->office->getId());
	        foreach ($raports as $raport) {
	            $raport->setWithdrawals($this->withdrawalDAOManager->forRaport($raport->getId()));
	        }
	    }else {
	        $raports = array();
	    }
	    
	    $request->addAttribute(self::ATT_MONTH, $month);
	    $request->addAttribute(self::ATT_MEMBERS, $members);
	    $request->addAttribute(self::ATT_VIRTUAL_MONEYS, $virtuals);
	    $request->addAttribute(self::ATT_GRADES_MEMBERS, $packets);
	    $request->addAttribute(self::ATT_WITHDRAWALS, $withdrawals);
	    $request->addAttribute(self::ATT_RAPORTS_WITHDRAWALS, $raports);
	}

}

