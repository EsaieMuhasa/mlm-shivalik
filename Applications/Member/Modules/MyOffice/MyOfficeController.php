<?php
namespace Applications\Member\Modules\MyOffice;

use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\RaportWithdrawal;
use Core\Shivalik\Entities\RequestVirtualMoney;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\RequestVirtualMoneyDAOManager;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use Core\Shivalik\Validators\RequestVirtualMoneyFormValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;
use PHPBackend\Http\HTTPController;

class MyOfficeController extends HTTPController
{
    const ATT_ACTIVE_ITEM_MENU = 'OFFICE_ACTIVE_ITEM_MENU';
    const ATT_ITEM_MENU_DASHBOARD = 'OFFICE_ACTIVE_ITEM_MENU_DASHBOARD';
    const ATT_ITEM_MENU_MEMBERS = 'OFFICE_ACTIVE_ITEM_MENU_MEMBERS';
    const ATT_ITEM_MENU_HISTORY = 'OFFICE_ACTIVE_ITEM_MENU_HISTORY';
    const ATT_ITEM_MENU_OFFICE_ADMIN = 'OFFICE_ACTIVE_ITEM_MENU_OFFICE_ADMIN';
    const ATT_ITEM_MENU_VIRTUAL_MONEY = 'OFFICE_ACTIVE_ITEM_MENU_VIRTUAL_MONEY';
    const ATT_ITEM_MENU_WITHDRAWALS = 'OFFICE_ACTIVE_ITEM_MENU_CASH_OUTS';
    
    
    const ATT_OFFICE = 'office';
    const ATT_CAN_SEND_RAPORT = 'canSendRaport';
    const ATT_OFFICE_ADMIN = 'officeAdmin';
    
    const ATT_MEMBERS = 'members';
    const ATT_GRADES_MEMBERS = 'gradesMembers';
    
    const ATT_OFFICE_SIZE = 'officeSize';
    const ATT_COUNT_MEMEBERS = 'COUNT_MEMBERS_IN_OFFICE';
    const ATT_COUNT_WITHDRAWALS = 'COUNT_WITHDRAWALS_BY_OFFICE';
    const ATT_WITHDRAWALS = 'WITHDRAWALS';
    const ATT_WITHDRAWALS_AMOUNT = 'WITHDRAWALS_AMOUNT';
    
    //pour les monais virtuels
    const ATT_VIRTUAL_MONEY = 'virtualMoney';
    const ATT_VIRTUAL_MONEYS = 'virtualMoneys';
    
    const ATT_MONTH = 'MONTH';
    const CONFIG_MAX_MEMBER_VIEW_STEP = 'maxMembers';
    const PARAM_MEMBER_COUNT = 'PARAM_MEMBER_COUNT';
    
    /**
     * @var GradeMemberDAOManager
     */
    private $gradeMemberDAOManager;
    
    /**
     * @var VirtualMoneyDAOManager
     */
    private $virtualMoneyDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * @var WithdrawalDAOManager
     */
    private $withdrawalDAOManager;
    
    /**
     * @var RequestVirtualMoneyDAOManager
     */
    private $requestVirtualMoneyDAOManager;
    
    /**
     * @var Office
     */
    private $office;
    
    /**
     * {@inheritDoc}
     * @see HTTPController::__construct()
     */
    public function __construct(Application $application, $module, string $action)
    {
        parent::__construct($application, $module, $action);
        $member = $application->getRequest()->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        if ($member->getOfficeAccount() == null) {
            $application->getResponse()->sendError();
        }
        $this->office = $member->getOfficeAccount();
    }
    
    /**
     * tableau de board d'un office
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_DASHBOARD);
        
        $nombreMembre = $this->memberDAOManager->countByOffice($this->office->getId());
        if ($this->withdrawalDAOManager->checkByOffice($this->office->getId(), null, null)) {
            $withdrawals = $this->withdrawalDAOManager->findByOffice($this->office->getId(), null, null);
        }else {
            $withdrawals = array();
        }
        
        if ($this->gradeMemberDAOManager->checkByOffice($this->office->getId())) {
            $this->office->setOperations($this->gradeMemberDAOManager->findByOffice($this->office->getId()));
        }
        
        if ($this->virtualMoneyDAOManager->checkByOffice($this->office->getId())) {
            $this->office->setVirtualMoneys($this->virtualMoneyDAOManager->findByOffice($this->office->getId()));
        }
        
        $this->office->setWithdrawals($withdrawals);
        $request->addAttribute(self::ATT_COUNT_MEMEBERS, $nombreMembre);
    }
    
    /**
     * Visualisation de cash outs deja effectuer dans l'office du proprietaire du compte
     * @param Request $request
     * @param Response $response
     */
    public function executeWithdrawals (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_WITHDRAWALS);
        
        $limit = $request->existInGET("limit")? intval($request->getDataGET("limit"), 10) : intval($request->getApplication()->getConfig()->get('limitCashout')->getValue(), 10);
        $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        $count = $this->withdrawalDAOManager->countByOffice($this->office->getId(), null, null);
        
        if ($this->withdrawalDAOManager->checkByOffice($this->office->getId(), null, null, $limit, $offset)) {
            $cashouts = $this->withdrawalDAOManager->findByOffice($this->office->getId(), null, null, $limit, $offset);
            $request->addAttribute(self::ATT_WITHDRAWALS, $cashouts);
        }else {
            $request->addAttribute(self::ATT_WITHDRAWALS, array());
        }
        
        $request->addAttribute(self::ATT_COUNT_WITHDRAWALS, $count);
    }
    
    /**
     *
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
     * Envoie requette demande monais virtuel
     * @param Request $request
     * @param Response $response
     */
    public function executeRequestVirtualmoney(Request $request, Response $response) : void {
        
        $response->sendRedirect("/member/office/virtualmoney/");
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new RequestVirtualMoneyFormValidator($this->getDaoManager());
            $request->addAttribute($form::FIELD_OFFICE, $this->office);
            $virtual = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/office/virtualmoney/");
            }
            
            $request->addAttribute(self::ATT_VIRTUAL_MONEY, $virtual);
            $form->includeFeedback($request);
        }
    }
    
    /**
     * Envoie du rapport mensuel du matching
     * cette action n'a pas de vue
     * @param Request $request
     * @param Response $response
     */
    public function executeSendRaportWithdrawals(Request $request, Response $response) : void {
        if (!$this->withdrawalDAOManager->checkByOffice($this->office->getId(), true, false)) {
            $response->sendError("impossible to perform this operation because it is active for a precise time limit.");
        }
        $request->addAttribute(self::ATT_OFFICE, $this->office);
        $form = new RequestVirtualMoneyFormValidator($this->getDaoManager());
        $raport = $form->sendMatchingAfterValidation($request);
        $request->addToast($form->buildToastMessage());
        $response->sendRedirect("/member/office/");
    }
    
    
    /**
     * affichage de membres qui se sont adherer, en passant par le bureau
     * @param Request $request
     * @param Response $response
     */
    public function executeMembers (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_ACTIVE_ITEM_MENU, self::ATT_ITEM_MENU_MEMBERS);
        
        $limit = $request->existInGET("limit")? intval($request->getDataGET("limit"), 10) : intval($request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)->getValue(), 10);
        $offset = $request->existInGET('offset')? intval($request->getDataGET('offset'), 10) : 0;
        $count = $this->memberDAOManager->countByOffice($this->office->getId());
        
        $members = [];
        if ($count > 0) {
            if($this->memberDAOManager->checkByOffice($this->office->getId(), $limit, $offset)) {                
                $members = $this->memberDAOManager->findByOffice($this->office->getId(), $limit, $offset);
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
        
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $count);
        $request->addAttribute(self::ATT_MEMBERS, $members);
    }
    
    /**
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
        if ($this->gradeMemberDAOManager->checkUpgradeHistory($dateMin, $dateMax, $this->office->getId())) {
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
        
        $request->addAttribute(self::ATT_MONTH, $month);
        $request->addAttribute(self::ATT_MEMBERS, $members);
        $request->addAttribute(self::ATT_VIRTUAL_MONEYS, $virtuals);
        $request->addAttribute(self::ATT_GRADES_MEMBERS, $packets);
        $request->addAttribute(self::ATT_WITHDRAWALS, $withdrawals);
    }


}

