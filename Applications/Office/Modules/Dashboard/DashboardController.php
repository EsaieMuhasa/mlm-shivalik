<?php

namespace Applications\Office\Modules\Dashboard;

use Library\HTTPRequest;
use Library\HTTPResponse;
use Library\Controller;
use Applications\Office\OfficeApplication;
use Managers\WithdrawalDAOManager;
use Managers\GradeMemberDAOManager;
use Managers\MemberDAOManager;
use Managers\VirtualMoneyDAOManager;
use Managers\RequestVirtualMoneyDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class DashboardController extends Controller {
	
	const ATT_SOLDE = 'solde';
	const ATT_SOLDE_WITHDRAWALS = 'soldeWithdrawals';
	const ATT_WITHDRAWALS = 'withdrawals';
	const PARAM_MEMBER_COUNT = 'countOfficeMembers';
	
	/**
	 * @var WithdrawalDAOManager
	 */
	private $withdrawalDAOManager;
	
	/**
	 * @var RequestVirtualMoneyDAOManager
	 */
	private $requestVirtualMoneyDAOManager;
	
	/**
	 * @var MemberDAOManager
	 */
	private $memberDAOManager;
	
	/**
	 * @var GradeMemberDAOManager
	 */
	private $gradeMemberDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManager;
	
	/**
	 * {@inheritDoc}
	 * @see \Library\Controller::__construct()
	 */
	public function __construct(\Library\Application $application, $action, $module) {
		parent::__construct ( $application, $action, $module );
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Dashboard");
	}

	
	 /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeIndex (HTTPRequest $request, HTTPResponse $response) : void{
        //$response->sendRedirect("/admin/members/");
        $office = OfficeApplication::getConnectedUser()->getOffice();
        
        if ($this->withdrawalDAOManager->hasRequest($office->getId(), null)) {
            $withdrawals = $this->withdrawalDAOManager->getOfficeRequests($office->getId(), null);
        }else {
            $withdrawals = array();
        }
        
        $office->setWithdrawals($withdrawals);
        
        if ($this->gradeMemberDAOManager->hasOperation($office->getId())) {
        	$office->setOperations($this->gradeMemberDAOManager->getOperations($office->getId()));
        }
        
        if ($this->virtualMoneyDAOManager->hasVirtualMoney($office->getId())) {
        	$office->setVirtualMoneys($this->virtualMoneyDAOManager->forOffice($office->getId()));
        }
        
        
        if ($this->withdrawalDAOManager->hasRequest($office->getId())) {
            $all = $this->withdrawalDAOManager->getOfficeRequests($office->getId());
        }else {
            $all = array();
        }        
        
        $request->addAttribute(self::ATT_WITHDRAWALS, $all);
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countCreatedBy($office->getId()));
    }
    
    /**
     * tableau de borde de monais virtuels
     * -affiches les matching deja faits
     * -les demandes de matching
     * -la liste de membres qui ont faitles demandes leurs matiching
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeVirtualMoney (HTTPRequest $request, HTTPResponse $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Dashboard virtual money");
    	
    	$office = OfficeApplication::getConnectedUser()->getOffice();
    	
    	if ($this->withdrawalDAOManager->hasRequest($office->getId(), null)) {
    		$withdrawals = $this->withdrawalDAOManager->getOfficeRequests($office->getId(), null);
    	}else {
    		$withdrawals = array();
    	}
    	
    	$office->setWithdrawals($withdrawals);
    	
    	if ($this->gradeMemberDAOManager->hasOperation($office->getId())) {
    		$office->setOperations($this->gradeMemberDAOManager->getOperations($office->getId()));
    	}
    	
    	if ($this->virtualMoneyDAOManager->hasVirtualMoney($office->getId())) {
    		$office->setVirtualMoneys($this->virtualMoneyDAOManager->forOffice($office->getId()));
    	}
    	
    	
    	if ($this->withdrawalDAOManager->hasRequest($office->getId())) {
    		$all = $this->withdrawalDAOManager->getOfficeRequests($office->getId());
    	}else {
    		$all = array();
    	}
    	
    	if ($this->requestVirtualMoneyDAOManager->hasRequest($office->getId())) {
    		
    	}
    	
    	$request->addAttribute(self::ATT_WITHDRAWALS, $all);
    	$request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countCreatedBy($office->getId()));
    }
    
    
    /***
     * Evoie d'une requette de demande de matching
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeRequestVirtualMoney (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Send request of virtual money");
    }

}

