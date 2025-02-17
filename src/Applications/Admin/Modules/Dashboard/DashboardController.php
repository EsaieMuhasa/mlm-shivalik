<?php

namespace Applications\Admin\Modules\Dashboard;

use Applications\Admin\AdminController;
use Core\Charts\PacketChartBuilder;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\OfficeBonus;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\OfficeSizeDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Graphics\ChartJS\ChartConfig;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use Core\Shivalik\Entities\MonthlyOrder;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\ToastMessage;

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
	
	const ATT_PURCHASE = "purchase";
	const ATT_DISPTCH_PURCHASE = "dispatchPurchase";
	
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
	 * @var RequestVirtualMoneyDAOManager
	 */
	private $requestVirtualMoneyDAOManager;
	
	/**
	 * @var MonthlyOrderDAOManager
	 */
	private $monthlyOrderDAOManager;
	
	/**
	 * 
	 * @param Application $application
	 * @param string $module
	 * @param string $action
	 */
	public function __construct(Application $application, string $module, string $action) {
		parent::__construct ( $application, $module , $action);
		$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Dashboard");
		
	}

    /**
     * visualisation des etat tres importate du systeme
     *
     * @param Request $request
     * @return void
     * @deprecated  15/09/2022
     * pour des rainson de performante, cette action est desonamin obsolette, et sera ssupprimer dans le versions envnir
     * utilize plutot l'action main pour juire de nouvelle performance des element du tabeau d board
     */
    public function executeIndex (Request $request) : void{
        
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
        
        $members = $this->memberDAOManager->findAll();
        
        $solde = $this->memberDAOManager->getSumAllAllAvailable();
        $withdrawal = $this->withdrawalDAOManager->getSumAllRequested();
        $served = $this->withdrawalDAOManager->getSumAllServed();
        
        $request->addAttribute(self::ATT_SOLDE, $solde);
        $request->addAttribute(self::ATT_SOLDE_WITHDRAWALS, $withdrawal);
        
        if ($this->withdrawalDAOManager->checkByOffice($this->getConnectedAdmin()->getOffice()->getId())) {
            $all = $this->withdrawalDAOManager->findByOffice($this->getConnectedAdmin()->getOffice()->getId());
        }else {
            $all = array();
        }
        
        if ($this->requestVirtualMoneyDAOManager->checkWaiting()) {
            $rapports = $this->requestVirtualMoneyDAOManager->findWaiting();
        }else {
            $rapports = [];
        }
        
        //re-achat en attante
        $purchase = 0;
        $now = new \DateTime();
        if($this->monthlyOrderDAOManager->checkByMonth(null, null, true)) {
            /**
             * @var MonthlyOrder[] $purchases
             */
            $purchases = $this->monthlyOrderDAOManager->findByMonth(null, null, true);
            foreach ($purchases as $p) {
                $purchase += $p->getAvailable();
            }
        }
        $dispatchable = intval($now->format('d'), 10) >= 26 && $purchase > 0;
        
        $request
            ->addAttribute(self::ATT_PURCHASE, $purchase)
            ->addAttribute(self::ATT_DISPTCH_PURCHASE, $dispatchable)
            ->addAttribute(self::ATT_RAPORT_WITHDRAWALS, $rapports)
            ->addAttribute(self::ATT_WITHDRAWALS, $all)
            ->addAttribute(self::ATT_SOLDE_WITHDRAWALS_SERVED, $served)
            ->addAttribute(self::PARAM_UPGRADES_COUNT, $this->gradeMemberDAOManager->countUpgrades())
            ->addAttribute(self::PARAM_MEMBER_COUNT, $this->memberDAOManager->countAll());
    }

    /**
     * nouveau dashboard de l'administration centrale
     *
     * @param Request $request
     * @return void
     */
    public function executeMain (Request $request) : void {

    }
    
    /**
     * Dispatching du bonus mensuel de re-achat
     * @param Request $request
     * @param Response $response
     */
    public function executeDispatchPurchase (Request $request, Response $response) : void {
        try {
            $this->monthlyOrderDAOManager->dispatchPurchaseBonus();
            
            $toast = new ToastMessage("Alert", "succesfull dispatching of purchase bonus", ToastMessage::MESSAGE_SUCCESS);
            $request->addToast($toast);
        } catch (DAOException $e) {
            $request->addToast(new ToastMessage("Error", $e->getMessage(), ToastMessage::MESSAGE_ERROR));
        }
        
        $response->sendRedirect("/admin/");
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

