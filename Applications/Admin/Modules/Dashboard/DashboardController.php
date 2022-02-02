<?php

namespace Applications\Admin\Modules\Dashboard;

use Applications\Admin\AdminApplication;
use Applications\Admin\AdminController;
use Core\Charts\PacketChartBuilder;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\RaportWithdrawalDAOManager;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\Member;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;

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
	
	const ATT_RAPORT_WITHDRAWALS = 'raportWithdrawals';
	
	const ATT_CHART_CONFIG = 'chartConfig';
	const ATT_GRADES = 'packets';
	const ATT_GENERATIONS = 'generations';
	const ATT_SIZES = 'sizes';//office sizes
	
	/**
	 * @var GradeDAOManager
	 */
	private $gradeDAOManager;
	
	/**
	 * @var GenerationDAOManager
	 */
	private $generationDAOManager;
	
	/**
	 * @var SizeDAOManager
	 */
	private $sizeDAOManager;
	
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
	 * @var RaportWithdrawalDAOManager
	 */
	private $raportWithdrawalDAOManager;
	
	
	public function __construct(Application $application, string $action, string $module) {
		parent::__construct ( $application, $action, $module );
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Dashboard");
	}

	
   /***
    * visualisation de l'etat actuel du systeme
    * @param Request $request
    * @param Response $response
    */
    public function executeIndex (Request $request, Response $response) : void{
        
        //virtualMoney
        /**
         * @var VirtualMoney[] $virtuals
         * @var VirtualMoney $virtual
         */
        $virtuals = $this->virtualMoneyDAOManager->findAll();
        foreach ($virtuals as $virtual) {
            
            if ((!$this->officeBonusDAOManager->checkByVirtual($virtual->getId())) && $virtual->getAmount() > 0) {//pour les virtual sans bonus

                $virtual->setOffice($this->officeDAOManager->findById($virtual->getOffice()->getId()));
                $virtual->getOffice()->setOfficeSize($this->officeSizeDAOManager->findCurrentByOffice($virtual->getOffice()->getId()));
                
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
        $members = $this->memberDAOManager->findAll();
        
        $solde = 0;
        $withdrawal = 0;
        $served = 0;
        
        foreach ($members as $member) {
            $account = $this->memberDAOManager->loadAccount($member);
            $solde += $account->getSolde();
            $withdrawal += $account->getWithdrawRequest();
        }
        
        $request->addAttribute(self::ATT_SOLDE, $solde);
        $request->addAttribute(self::ATT_SOLDE_WITHDRAWALS, $withdrawal);
        
        /**
         * @var Withdrawal[] $allWithdrawals
         * @var Withdrawal $withd
         */
        $allWithdrawals = $this->withdrawalDAOManager->findAll();
        foreach ($allWithdrawals as $withd) {
            if ($withd->getAdmin() != null && $withd->getRaport()==null && $withd->getOffice()->getId() != AdminApplication::getConnectedUser()->getOffice()->getId()) {
                $served += $withd->getAmount();
            }
        }
        
        if ($this->withdrawalDAOManager->checkByOffice(AdminApplication::getConnectedUser()->getOffice()->getId())) {
            $all = $this->withdrawalDAOManager->findByOffice(AdminApplication::getConnectedUser()->getOffice()->getId());
        }else {
            $all = array();
        }
        
        if ($this->raportWithdrawalDAOManager->countAll() != 0) {
            $rapports = $this->raportWithdrawalDAOManager->findAll();
        }else {
            $rapports = [];
        }
        
        $request->addAttribute(self::ATT_RAPORT_WITHDRAWALS, $rapports);
        $request->addAttribute(self::ATT_WITHDRAWALS, $all);
        $request->addAttribute(self::ATT_SOLDE_WITHDRAWALS_SERVED, $served);
        $request->addAttribute(self::PARAM_UPGRADES_COUNT, $this->gradeMemberDAOManager->countUpgrades());
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countAll());
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeStatistics(Request $request, Response $response) : void{
        
    }
    
    /**
     * generation du catalogue chartJS pour dessinder le graphique
     * @param Request $request
     * @param Response $response
     */
    public function executeChartPackets(Request $request, Response $response) : void{
        $members = $this->memberDAOManager->findAll();
        /**
         * @var Member $member
         */
        foreach ($members as $member) {
            $member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($member->getId()));
        }
        
        $builder = new PacketChartBuilder($request->getApplication()->getConfig(), array(), $members);
        $builder->getChart()->getConfig()->setType(ChartConfig::TYPE_DOUGHNUT_CHART);
        $request->addAttribute(self::ATT_CHART_CONFIG, $builder->getChart());
    }
    
    /**
     * Afffichage de la configuration actuel du reseau
     * -les packets
     * -la configuration des bonus negerationnnels
     * @param Request $request
     */
    public function executeSettings (Request $request) : void {
        
        if ($this->gradeDAOManager->hasData()) {
            $grades = $this->gradeDAOManager->findAll();
        } else {
            $grades = [];
        }
        
        if ($this->generationDAOManager->hasData()) {
            $generations =  $this->generationDAOManager->findAll();
        } else {
            $generations = [];
        }
        
        if ($this->sizeDAOManager->hasData()) {
            $sizes = $this->sizeDAOManager->findAll();
        } else {
            $sizes = [];
        }
        
        $request->addAttribute(self::ATT_SIZES, $sizes);
        $request->addAttribute(self::ATT_GENERATIONS, $generations);
        $request->addAttribute(self::ATT_GRADES, $grades);
    }

}

