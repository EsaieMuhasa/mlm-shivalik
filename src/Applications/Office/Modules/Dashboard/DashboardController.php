<?php

namespace Applications\Office\Modules\Dashboard;

use Core\Shivalik\Entities\Office;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Managers\MoneyGradeMemberDAOManager;
use DateTime;
use PHPBackend\Calendar\Month;
use PHPBackend\Response;


/**
 *
 * @author Esaie MHS
 *        
 */
class DashboardController extends HTTPController {
	
	const ATT_SOLDE = 'solde';
	const ATT_SOLDE_WITHDRAWALS = 'soldeWithdrawals';
	const ATT_WITHDRAWALS = 'withdrawals';
	const PARAM_MEMBER_COUNT = 'countOfficeMembers';
	const ATT_MONTH = 'month';
	
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
	 * @var MoneyGradeMemberDAOManager
	 */
	private $moneyGradeMemberDAOManager;
	
	/**
	 * {@inheritDoc}
	 * @see HTTPController::__construct()
	 */
	public function __construct(Application $application, $module, $action) {
		parent::__construct ( $application, $module , $action);
		$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Dashboard");
	}

	
	 /**
     * @param Request $request
     * @param Request $response
     */
    public function executeIndex (Request $request, Response $response) : void{
        //$response->sendRedirect("/admin/members/");
        $office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
        
        if ($this->withdrawalDAOManager->checkByOffice($office->getId(), false, false)) {
            $withdrawals = $this->withdrawalDAOManager->findByOffice($office->getId(), false, false);
        }else {
            $withdrawals = array();
        }
        
        if ($this->gradeMemberDAOManager->checkByOffice($office->getId())) {
        	$office->setOperations($this->gradeMemberDAOManager->findByOffice($office->getId()));
        }
        
        if ($this->virtualMoneyDAOManager->checkByOffice($office->getId())) {
        	$office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($office->getId()));
        }
        
        
        if ($this->withdrawalDAOManager->checkByOffice($office->getId(), null, false)) {
            $all = $this->withdrawalDAOManager->findByOffice($office->getId(), null, false);
        }else {
            $all = array();
        }
        
        $office->setWithdrawals($all);
        
        $request->addAttribute(self::ATT_WITHDRAWALS, $withdrawals);
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countByOffice($office->getId()));
    }
    
    /**
     * tableau de borde de monais virtuels
     * -affiches les matching deja faits
     * -les demandes de matching
     * -la liste de membres qui ont faitles demandes leurs matiching
     * @param Request $request
     * @param Request $response
     */
    public function executeVirtualMoney (Request $request, Response $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Dashboard virtual money");
    	
    	$office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
    	
    	if ($this->withdrawalDAOManager->checkByOffice($office->getId(), null)) {
    		$withdrawals = $this->withdrawalDAOManager->findByOffice($office->getId(), null);
    	}else {
    		$withdrawals = array();
    	}
    	
    	$office->setWithdrawals($withdrawals);
    	
    	if ($this->gradeMemberDAOManager->checkByOffice($office->getId())) {
    		$office->setOperations($this->gradeMemberDAOManager->findByOffice($office->getId()));
    	}
    	
    	if ($this->virtualMoneyDAOManager->checkByOffice($office->getId())) {
    		$office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($office->getId()));
    	}
    	
    	
    	if ($this->withdrawalDAOManager->checkByOffice($office->getId())) {
    		$all = $this->withdrawalDAOManager->findByOffice($office->getId());
    	} else {
    		$all = array();
    	}
    	
    	$request->addAttribute(self::ATT_WITHDRAWALS, $all);
    	$request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countByOffice($office->getId()));
    }
    
    
    /***
     * Evoie d'une requette de demande de matching
     * @param Request $request
     * @param Request $response
     */
    public function executeRequestVirtualMoney (Request $request, Request $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Send request of virtual money");
    }

	public function executeVirtualMoneyHistory (Request $request) : void
	{
		$dayDate = $request->getDataGET('date');
		/**
		 * @var Office $office
		 */
		$office = $request->getSession()->getAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)->getOffice();
		
		if ($dayDate == null) {
			$date = new DateTime($request->getDataGET('date') ? $request->getDataGET('date') :  'now');
			$mIndex = $request->getDataGET('monthIndex') ? $request->getDataGET('monthIndex') : intval($date->format('m'), 10);
			$yIndex = $request->getDataGET('yearIndex') ? $request->getDataGET('yearIndex') : intval($date->format('Y'), 10);

			$month = new Month($mIndex, $yIndex);
			$operations = $this->moneyGradeMemberDAOManager->findByOffice($office, $month->getFirstDay(), $month->getLastDay());
		} else  {
			$date = new DateTime($dayDate);
			$month = new Month(intval($date->format('m'), 10), intval($date->format('Y'), 10));
			$month->addSelectedDate($date);
			$operations = $this->moneyGradeMemberDAOManager->findByOffice($office, $date);
		}


		
		$request->addAttribute('moneys', $operations);
		$request->addAttribute(self::ATT_MONTH, $month);
	}

}

