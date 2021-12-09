<?php

namespace Applications\Admin\Modules\Dashboard;

use Library\HTTPRequest;
use Library\HTTPResponse;
use Applications\Admin\AdminApplication;
use Applications\Admin\AdminController;
use Managers\GradeMemberDAOManager;
use Entities\Withdrawal;
use Managers\VirtualMoneyDAOManager;
use Entities\VirtualMoney;
use Managers\OfficeBonusDAOManager;
use Managers\OfficeDAOManager;
use Managers\OfficeSizeDAOManager;
use Entities\OfficeBonus;

/**
 *
 * @author Esaie MHS
 *        
 */
class DashboardController extends AdminController {
	
	const ATT_SOLDE = 'solde';
	const ATT_SOLDE_WITHDRAWALS = 'soldeWithdrawals';
	const ATT_SOLDE_WITHDRAWALS_SERVED = 'soldeWithdrawalsServed';
	const ATT_WITHDRAWALS = 'withdrawals';
	const PARAM_UPGRADES_COUNT = 'countUpgrades';
	const ATT_VIRTUAL_MONEYS = "virtualMoneys";
	
	/**
	 * @var GradeMemberDAOManager
	 */
	private $gradeMemberDAOManager;
	
	/**
	 * @var VirtualMoneyDAOManager
	 */
	private $virtualMoneyDAOManager;
	
	/**
	 * @var OfficeBonusDAOManager
	 */
	private $officeBonusDAOManager;
	
	/**
	 * @var OfficeDAOManager
	 */
	private $officeDAOManager;
	
	/**
	 * @var OfficeSizeDAOManager
	 */
	private $officeSizeDAOManager;
	
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
        
        //virtualMoney
        /**
         * @var VirtualMoney[] $virtuals
         * @var VirtualMoney $virtual
         */
        $virtuals = $this->virtualMoneyDAOManager->getAll();
        foreach ($virtuals as $virtual) {
            
            if ((!$this->officeBonusDAOManager->virtualHasBonus($virtual->getId())) && $virtual->getAmount() > 0) {//pour les virtual sans bonus

                $virtual->setOffice($this->officeDAOManager->getForId($virtual->getOffice()->getId()));
                $virtual->getOffice()->setOfficeSize($this->officeSizeDAOManager->getCurrent($virtual->getOffice()->getId()));
                
                $bonus = new OfficeBonus();
                
                $amount = ($virtual->getAmount()/100) * $virtual->getOffice()->getOfficeSize()->getSize()->getPercentage();
                $bonus->setAmount($amount);
                $bonus->setMember($virtual->getOffice()->getMember());
                $bonus->setGenerator($virtual->getOffice()->getOfficeSize());
                $bonus->setVirtualMoney($virtual);
                $this->officeBonusDAOManager->create($bonus);
            }
        }
        //\\virtualMoney
        
        //$response->sendRedirect("/admin/members/");
        $members = $this->memberDAOManager->getAll();
        
        $solde = 0;
        $withdrawal = 0;
        $served = 0;
        
        foreach ($members as $member) {
            $account = $this->getAccount($member);
            $solde += $account->getSolde();
            $withdrawal += $account->getWithdrawRequest();
        }
        
        $request->addAttribute(self::ATT_SOLDE, $solde);
        $request->addAttribute(self::ATT_SOLDE_WITHDRAWALS, $withdrawal);
        
        /**
         * @var Withdrawal[] $allWithdrawals
         * @var Withdrawal $withd
         */
        $allWithdrawals = $this->withdrawalDAOManager->getAll();
        foreach ($allWithdrawals as $withd) {
            if ($withd->getAdmin() != null && $withd->getRaport()==null && $withd->getOffice()->getId() != AdminApplication::getConnectedUser()->getOffice()->getId()) {
                $served += $withd->getAmount();
            }
        }
        
        if ($this->withdrawalDAOManager->hasRequest(AdminApplication::getConnectedUser()->getOffice()->getId())) {
            $all = $this->withdrawalDAOManager->getOfficeRequests(AdminApplication::getConnectedUser()->getOffice()->getId());
        }else {
            $all = array();
        }
        
        $request->addAttribute(self::ATT_WITHDRAWALS, $all);
        $request->addAttribute(self::ATT_SOLDE_WITHDRAWALS_SERVED, $served);
        $request->addAttribute(self::PARAM_UPGRADES_COUNT, $this->gradeMemberDAOManager->countUpgrades());
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countAll());
    }

}

