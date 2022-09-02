<?php

namespace Applications\Office\Modules\Members;

use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Managers\BonusGenerationDAOManager;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use Core\Shivalik\Validators\GradeMemberFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;
use Core\Shivalik\Validators\MemberFormValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\ToastMessage;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Managers\SellSheetRowDAOManager;
use Core\Shivalik\Validators\SellSheetRowFormValidator;
use PHPBackend\Response;

/**
 *
 * @author Esaie MHS
 *        
 */
class MembersController extends HTTPController {
	const CONFIG_MAX_MEMBER_VIEW_STEP = 'maxMembers';
	const PARAM_MEMBER_COUNT = 'countMembers';
	
	const ATT_COUNTRYS = 'countrys';
	const ATT_LOCALISATION = 'localisation';
	const ATT_COMPTE = 'compte';
	const ATT_MEMBERS = 'members';
	const ATT_MEMBER = 'member';
	const ATT_SPONSOR = 'sponsorMember';
	const ATT_GRADE_MEMBER = 'gradeMember';
	const ATT_REQUESTED_GRADE_MEMBER = 'RequestedGradeMember';
	const ATT_GRADES = 'grades';
	const ATT_SOLDE = 'solde';
	const ATT_SOLDE_WITHDRAWALS = 'soldeWithdrawals';
	const ATT_WITHDRAWALS = 'withdrawals';

	const ATT_SELL_SHEET_ROW = 'sellSheetRow';
	const ATT_SELL_SHEET_ROWS = 'sellSheetRows';
	const ATT_PRODUCTS = 'products';
	
	const LEFT_CHILDS = 'LEFT';
	const MIDDLE_CHILDS = 'MIDDLE';
	const RIGHT_CHILDS = 'RIGHT';
	
	const ATT_MONTHLY_ORDER_FOR_ACCOUNT = 'MONTHLY_ORDER_FOR_ACCOUNT';
	
	/**
	 * @var MemberDAOManager
	 */
	private $memberDAOManager;
	
	/**
	 * @var GradeDAOManager
	 */
	private $gradeDAOManager;
	
	/**
	 * @var CountryDAOManager
	 */
	private $countryDAOManager;
	
	/**
	 * @var PointValueDAOManager
	 */
	private $pointValueDAOManager;
	
	/**
	 * @var GradeMemberDAOManager
	 */
	private $gradeMemberDAOManager;
	
	/**
	 * @var BonusGenerationDAOManager
	 */
	private $bonusGenerationDAOManager;
	
	/**
	 * @var WithdrawalDAOManager
	 */
	private $withdrawalDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManager;
	
	/**
	 * @var MonthlyOrderDAOManager
	 */
	private $monthlyOrderDAOManager;
	
	/**
	 * @var OfficeDAOManager
	 */
	private $officeDAOManager;

	/**
	 * @var ProductDAOManager
	 */
	private $productDAOManager;

	/**
	 * @var SellSheetRowDAOManager
	 */
	private $sellSheetRowDAOManager;
	
	/**
	 * {@inheritDoc}
	 * @see HTTPController::__construct()
	 */
	public function __construct(Application $application, string $module, string $action)
	{
		parent::__construct($application, $module, $action);
		$nombre = $this->memberDAOManager->countAll();
		$application->getRequest()->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
		
		if ($application->getRequest()->existInGET('id')) {//
			$id = intval($application->getRequest()->getDataGET('id'), 10);
			/**
			 * @var Member $member
			 */
			$member = $this->memberDAOManager->findById($id);
			$account = $this->getAccount($member);
			
			$application->getRequest()->addAttribute(self::ATT_COMPTE, $account);
			$application->getRequest()->addAttribute(self::ATT_MEMBER, $member);
			$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, $member->getNames());
		} else {
			$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Union members");
		}
	}
	
	/**
	 * @param Member $member
	 * @return Account
	 */
	public function getAccount (Member $member) : Account {
		return $this->memberDAOManager->loadAccount($member);
	}
	
	/**
	 * acces aux membres, dont leurs compte ont ete creer par l'office dans la session encours.
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeIndex (Request $request, Response $response) : void {
		
		$office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
		
		$limit = intval($request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)->getValue(), 10);
		$offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
		
		if ($request->getMethod() == Request::HTTP_POST) {
		    
		    $matricule = $request->getDataPOST('id');
		    if ($matricule == null ) {
		        $message = new ToastMessage('Error', "Enter user ID to perform shearch operation...", ToastMessage::MESSAGE_ERROR);
		    } else if ($this->memberDAOManager->checkByMatricule($matricule)) {
				$member = $this->memberDAOManager->findByMatricule($request->getDataPOST('id'));
				$response->sendRedirect("/office/members/{$member->getId()}/");
			}else {
    			$message = new ToastMessage('Error', "Know user ID in system. ID: {$request->getDataPOST('id')}", ToastMessage::MESSAGE_ERROR);
			}
			
			$request->addToast($message);
			$response->sendRedirect('/office/members/');
		}
		
		$count = $this->memberDAOManager->countByOffice($office->getId());
		$members = [];
		if ($count > 0) {
			if ($this->memberDAOManager->checkByOffice($office->getId(), $limit, $offset)) {
				$members = $this->memberDAOManager->findByOffice($office->getId(), $limit, $offset);
			} else {
			    $response->sendError();
			}
		}
		
		/**
		* @var Member $member
		*/
		foreach ($members as $member) {
		    if ($this->gradeMemberDAOManager->checkCurrentByMember($member->getId())) {
    		    $member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($member->getId()));
		    }
		}
		$request->addAttribute(self::ATT_MEMBERS, $members);
		$request->addAttribute(self::PARAM_MEMBER_COUNT, $count);
	}
	
	
	
	/**
	 * Adhesion d'un nouveau membre, sur wallet de l'office
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeAddMember (Request $request, Response $response) : void {
		
		$office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
		
		$office = $this->officeDAOManager->load($office);
		
		if ($this->gradeMemberDAOManager->checkByOffice($office->getId())) {
			$office->setOperations($this->gradeMemberDAOManager->findByOffice($office->getId()));
		}
		
		if ($this->virtualMoneyDAOManager->checkByOffice($office->getId())) {
			$office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($office->getId()));
		}
		
		if ($request->getMethod() == Request::HTTP_POST) {
		    /**
		     * on prefere passer pas L'inscription au pack, qui utilise dans le colices 
		     * le validateur d'un membre et tout les validateurs qui y sont lier
		     * @var \Core\Shivalik\Validators\GradeMemberFormValidator $form
		     */
			$form = new GradeMemberFormValidator($this->getDaoManager());
			$request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION));
			$gm = $form->createAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/office/members/");
			}
			
			$request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
			$request->addAttribute(MemberFormValidator::MEMBER_FEEDBACK, $form->getFeedback(MemberFormValidator::MEMBER_FEEDBACK));
			$form->includeFeedback($request);
			
			$request->addAttribute(self::ATT_GRADE_MEMBER, $gm);
			$request->addAttribute(self::ATT_MEMBER, $gm->getMember());
			$request->addAttribute(self::ATT_LOCALISATION, $gm->getMember()->getLocalisation());
		}
		
		$request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
		$grades = $this->gradeDAOManager->findAll();
		$request->addAttribute(self::ATT_GRADES, $grades);
	}
	
	/**
	 * affiliation d'un membre.
	 * la facturation se fait sur le compte du sponsor du compte
	 * @param Request $request
	 * @param Response $response
	 */
	public function executeAffiliateMember (Request $request, Response $response) : void {
	    
	    $office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
	    
	    if ($this->gradeMemberDAOManager->checkByOffice($office->getId())) {
	        $office->setOperations($this->gradeMemberDAOManager->findByOffice($office->getId()));
	    }
	    
	    if ($this->virtualMoneyDAOManager->checkByOffice($office->getId())) {
	        $office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($office->getId()));
	    }
	    
	    $id = intval($request->getDataGET('id'), 10);
	    if (!$this->memberDAOManager->checkById($id)) {
	        $response->sendError();
	    }
	    
	    /**
	     * @var Member $member
	     */
	    $member = $this->memberDAOManager->findById($id);
	    
	    if ($this->monthlyOrderDAOManager->checkByMemberOfMonth($member->getId())) {
	        $monthly = $this->monthlyOrderDAOManager->findByMemberOfMonth($member->getId());
			if($monthly->getAvailable() < 50) {
				$response->sendRedirect("/office/members/{$id}/");
			}
	        $request->addAttribute(self::ATT_MONTHLY_ORDER_FOR_ACCOUNT, $monthly);
	    } else {
	        $response->sendRedirect("/office/members/{$id}/");
	    }
	    $request->addAttribute(MemberFormValidator::FIELD_SPONSOR, $member);
	    $request->addAttribute(self::ATT_SPONSOR, $member);
	    $request->addAttribute(self::ATT_MEMBER, null);
	    
	    if ($request->getMethod() == Request::HTTP_POST) {
	        /**
	         * on prefere passer pas L'inscription au pack, qui utilise dans le colices
	         * le validateur d'un membre et tout les validateurs qui y sont lier
	         * @var \Core\Shivalik\Validators\GradeMemberFormValidator $form
	         */
	        $form = new GradeMemberFormValidator($this->getDaoManager());
	        $request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION));
	        $gm = $form->affiliateAfterValidation($request);
	        
	        if (!$form->hasError()) {
	            $response->sendRedirect("/office/members/");
	        }
	        
	        $request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
	        $request->addAttribute(MemberFormValidator::MEMBER_FEEDBACK, $form->getFeedback(MemberFormValidator::MEMBER_FEEDBACK));
	        $form->includeFeedback($request);
	        
	        $request->addAttribute(self::ATT_GRADE_MEMBER, $gm);
	        $request->addAttribute(self::ATT_MEMBER, $gm->getMember());
	        $request->addAttribute(self::ATT_LOCALISATION, $gm->getMember()->getLocalisation());
	    }
	    
	    $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
	    $grades = $this->gradeDAOManager->findAll();
	    $request->addAttribute(self::ATT_GRADES, $grades);
	}
	
	
	/**
	 * Dashboard du compte d'un membre
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeMember (Request $request, Response $response) : void {
		$id = intval($request->getDataGET('id'), 10);
		if (!$this->memberDAOManager->checkById($id)) {
			$response->sendError();
		}
		
		/**
		 * @var Member $member
		 */
		$member = $this->memberDAOManager->findById($id);
		
		if ($this->gradeMemberDAOManager->checkCurrentByMember($member->getId())) {
			$gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($id);
			$gradeMember->setMember($member);
			$request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
		} 
		
		if ($this->gradeMemberDAOManager->checkRequestedByMember($member->getId())) {
			$requestedGradeMember = $this->gradeMemberDAOManager->findRequestedByMember($member->getId());
			$requestedGradeMember->setMember($member);
			$request->addAttribute(self::ATT_REQUESTED_GRADE_MEMBER, $requestedGradeMember);
		}
		
		if($this->monthlyOrderDAOManager->checkByMemberOfMonth($member->getId())){
		    $monthly = $this->monthlyOrderDAOManager->findByMemberOfMonth($member->getId());
		    $request->addAttribute(self::ATT_MONTHLY_ORDER_FOR_ACCOUNT, $monthly);
		}
		
		//Chargement du compte
		$compte = $this->getAccount($member);
		
		$request->addAttribute(self::ATT_COMPTE, $compte);
		$request->addAttribute(self::ATT_MEMBER, $member);
	}
	
	/**
	 * visiualisation des membres du reseau d'un membre
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeDownlines (Request $request, Response $response) : void {
		
		$id = intval($request->getDataGET('id'), 10);
		if (!$this->memberDAOManager->checkById($id)) {
			$response->sendError();
		}
		
		/**
		 * @var Member $member
		 */
		$member = $this->memberDAOManager->findById($id);
		
		
		if ($request->existInGET('foot')) {
			//chargement des downlines
			switch ($request->getDataGET('foot')){
				case 'left' : {//left
					$members = $this->memberDAOManager->findLeftDownlinesChilds($member->getId());
				}break;
				
				case 'middle' : {//middle
					$members = $this->memberDAOManager->findMiddleDownlinesChilds($member->getId());
				}break;
				
				case 'right' : {//right
					$members = $this->memberDAOManager->findRightDownlinesChilds($member->getId());
				}break;
				
				default : {//all Member
					$members = $this->memberDAOManager->findDownlinesChilds($member->getId());
				}
			}
			
			$request->addAttribute(self::ATT_MEMBERS, $members);
			
		}else {
			
			//comptage des downlines
			$left = $this->memberDAOManager->countLeftChild($member->getId());
			$middle = $this->memberDAOManager->countMiddleChild($member->getId());
			$right = $this->memberDAOManager->countRightChild($member->getId());
			
			$request->addAttribute(self::LEFT_CHILDS, $left);
			$request->addAttribute(self::MIDDLE_CHILDS, $middle);
			$request->addAttribute(self::RIGHT_CHILDS, $right);
		}
		
		$request->addAttribute(self::ATT_MEMBER, $member);
		
		$account = $this->getAccount($member);
		$account->calcul();
		$request->addAttribute(self::ATT_COMPTE, $account);
	}
	
	/**
	 * - visualisation des operations de cashout deja effectuer par le compte d'un utilisateur
	 * - validation d'une operation pour le compte d'un utilisateur
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeWithdrawalsMember (Request $request, Response $response) : void {
		$id = intval($request->getDataGET('id'), 10);//identifiant du membre
		if (!$this->memberDAOManager->checkById($id)) {
			$response->sendError();
		}
		
		/**
		 * @var Member $member
		 */
		$member = $this->memberDAOManager->findById($id);
		
		if ($request->existGET('requestId')) {//dans le cas où on doit accepte le cashout
		    
		    /**
		     * @var Withdrawal $cashout
		     */
		    $cashout = $this->withdrawalDAOManager->findById(intval($request->getDataGET('requestId'), 10));
		    if($cashout->getOffice()->getId() != $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->office->getId() || 
		        $cashout->getAdmin() != null) { //Dans le cas où, le cash out est deja valider ou que celui-ci a été envoyer à un office different de celui qui est dans la session encours
		        $response->sendError();
		    }
			$this->withdrawalDAOManager->validate($cashout->getId(), $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getId());
			$response->sendRedirect("/office/");
		}
		
		if ($this->gradeMemberDAOManager->checkCurrentByMember($member->getId())) {
			$gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($id);
			$gradeMember->setMember($member);
			$request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
		}
		
		if ($this->gradeMemberDAOManager->checkRequestedByMember($member->getId())) {
			$requestedGradeMember = $this->gradeMemberDAOManager->findRequestedByMember($member->getId());
			$requestedGradeMember->setMember($member);
			$request->addAttribute(self::ATT_REQUESTED_GRADE_MEMBER, $requestedGradeMember);
		}
		
		//Chargement des PV;
		$compte = $this->getAccount($member);
		
		if ($this->withdrawalDAOManager->checkByMember($member->getId())) {
			$withdrawals = $this->withdrawalDAOManager->findByMember($member->getId());
		}else {
			$withdrawals = array();
		}
		
		$request->addAttribute(self::ATT_WITHDRAWALS, $withdrawals);
		
		$request->addAttribute(self::ATT_COMPTE, $compte);
		$request->addAttribute(self::ATT_MEMBER, $member);
	}
	
	/**
	 * Mise en jours du packet d'un membre
	 * @param Request $request
	 * @param Request $response
	 */
	public function executeUpgradeMember (Request $request, Response $response) : void {
		$id = intval($request->getDataGET('id'), 10);
		if (!$this->memberDAOManager->checkById($id)) {
			$response->sendError();
		}
		
		if ($this->gradeMemberDAOManager->checkRequestedByMember($id)) {
			$response->sendRedirect("/office/members/{$id}/");
		}
		
		$office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
		
		if ($this->gradeMemberDAOManager->checkByOffice($office->getId())) {
			$office->setOperations($this->gradeMemberDAOManager->findByOffice($office->getId()));
		}
		
		if ($this->virtualMoneyDAOManager->checkByOffice($office->getId())) {
			$office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($office->getId()));
		}
		
		/**
		 * @var Member $member
		 */
		$member = $this->memberDAOManager->findById($id);
		$gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($id);
		$gradeMember->setMember($member);
		
		if ($request->getMethod() == Request::HTTP_POST) {
			$form = new GradeMemberFormValidator($this->getDaoManager());
			$request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION));
			$request->addAttribute($form::FIELD_MEMBER, $member->getId());
			$gradeMember = $form->upgradeAfterValidation($request);
			
			if (!$form->hasError()) {
				$response->sendRedirect("/office/members/{$id}/");
			}
			
			$request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
			$form->includeFeedback($request);
		}
		
		$request->addAttribute(self::ATT_MEMBER, $member);
		$request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
		$grades = $this->gradeDAOManager->findAll();
		$request->addAttribute(self::ATT_GRADES, $grades);
	}

	/**
	 * affiche la fiche de vente du compte membre dont le compte est encours de consultation
	 * @param Request $request
	 * @param Response $response
	 * @return void
	 */
	public function executeSellSheet (Request $request, Response $response ) : void {
		$id = intval($request->getDataGET('id'), 10);
		if (!$this->memberDAOManager->checkById($id)){
			$response->sendError();
		}

		$member = $this->memberDAOManager->findById($id);
		$limit = 12;
		$offset = 0;

		if ($this->sellSheetRowDAOManager->checkByMember($id, $offset)) {
			$rows = $this->sellSheetRowDAOManager->findByMember($id, $limit, $offset);
		} else {
			$rows = [];
		}

		$request->addAttribute(self::ATT_SELL_SHEET_ROWS, $rows);
	}

	/**
	 * ajout d'une nouvelle ligne sur la fiche d'un membre
	 * @param Request $request
	 * @param Response $response
	 * @return void
	 */
	public function executeAddSellSheetRow (Request $request, Response $response ) : void {

		if ($request->getMethod() == Request::HTTP_POST) {
			$member = new Member(['id' => $request->getDataGET('id')]);
			$form = new SellSheetRowFormValidator($this->getDaoManager());
			$request
				->addAttribute($form::ATT_OFFICE, $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice())
				->addAttribute($form::ATT_MEMBER, $member);

			$row = $form->createAfterValidation($request);
			if (!$form->hasError()) {
				$request->addToast($form->buildToastMessage());
				$response->sendRedirect("/office/members/{$member->getId()}/sell-sheet/");
			}
			$form->includeFeedback($request);
			$request->addAttribute(self::ATT_SELL_SHEET_ROW, $row);
		}
		$products = $this->productDAOManager->findSortedByName();
		$request->addAttribute(self::ATT_PRODUCTS, $products);
	}
	
}

