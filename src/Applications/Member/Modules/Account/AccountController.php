<?php
namespace Applications\Member\Modules\Account;

use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Managers\BonusGenerationDAOManager;
use Core\Shivalik\Managers\BudgetRubricDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use Core\Shivalik\Managers\PurchaseBonusDAOManager;
use Core\Shivalik\Managers\SellSheetRowDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use Core\Shivalik\Validators\WithdrawalFormValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;
use PHPBackend\Http\HTTPController;
use PHPBackend\Image2D\Mlm\TreeFormatter;

/**
 *
 * @author Esaie MHS
 *        
 */
class AccountController extends HTTPController
{
    const ATT_MEMBERS = 'members';
    const ATT_MEMBER = 'member';
    const ATT_GRADE_MEMBER = 'gradeMember';
    const ATT_ACCOUNT = 'account';
    const ATT_OFFICES = 'offices';
    const ATT_WITHDRAWEL = 'withdrawal';
    const ATT_WITHDRAWELS = 'withdrawals';
    const ATT_SELL_SHEETS =  'sellSheets';
    
    const LEFT_CHILDS = 'LEFT';
    const MIDDLE_CHILDS = 'MIDDLE';
    const RIGHT_CHILDS = 'RIGHT';
    
    const PARAM_DOWNLINE_COUNT = 'PARAM_DOWNLINE_COUNT';
    const ATT_TREE_FORMATTER = 'TREE_FORMATTER';
    
    const ATT_MONTH = 'month';
    const ATT_BONUS_GENERATIONS = 'bonusGeneration';
    const ATT_PURCHASE_BONUS = 'purchaseBonus';
    const ATT_BONUS_SPONSORING = 'bonusSponsoring';
    const ATT_POINTS_VALUES = 'pointValues';
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
     * @var GradeMemberDAOManager
     */
    private $gradeMemberDAOManager;
    
    /**
     * @var BonusGenerationDAOManager
     */
    private $bonusGenerationDAOManager;
    
    /**
     * @var OfficeBonusDAOManager
     */
    private $officeBonusDAOManager;
    
    /**
     * @var PointValueDAOManager
     */
    private $pointValueDAOManager;
    
    /**
     * @var WithdrawalDAOManager
     */
    private $withdrawalDAOManager;
    
    /**
     * @var PurchaseBonusDAOManager
     */
    private $purchaseBonusDAOManager;
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;

    /**
     * @var SellSheetRowDAOManager
     */
    private $sellSheetRowDAOManager;

    /**
     * @var BudgetRubricDAOManager
     */
    private $budgetRubricDAOManager;
    
    
    /**
     * {@inheritDoc}
     * @see HTTPController::__construct()
     */
    public function __construct(Application $application, string $module, string $action)
    {
        parent::__construct($application, $module, $action);
        $application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Account");
    }
    
    /**
     * @return Account
     */
    private function getAccount () : Account {
        $member = $this->getApplication()->getRequest()->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        return $this->memberDAOManager->loadAccount($member);
    }

    /**
     * index du compte d'un membre 
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Dashboard");
        
        /**
         * @var Member $member
         */
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        $gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($member->getId());
        $gradeMember->setMember($member);
        $member->setParticularOperation($this->budgetRubricDAOManager->checkOwnedByMember($member->getId()));
        
        $compte = $this->getAccount();
        
        $compte->calcul();
        
        $request->addAttribute(self::ATT_ACCOUNT, $compte);
        $request->addAttribute(self::ATT_MEMBER, $member);
        $request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeDownlines (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Downlines");
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        
        
        if ($request->existInGET('foot')) {
            //chargement des downlines
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $members = $this->memberDAOManager->findLeftDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countLeftChild($member->getId());
                }break;
                
                case 'middle' : {//middle
                    $members = $this->memberDAOManager->findMiddleDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countRightChild($member->getId());
                }break;
                
                case 'right' : {//right
                    $members = $this->memberDAOManager->findRightDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countMiddleChild($member->getId());
                }break;
                
                default : {//all Member
                    $members = $this->memberDAOManager->findDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countChilds($member->getId());
                }
            }
            $request->addAttribute(self::PARAM_DOWNLINE_COUNT, $count);
            $request->addAttribute(self::ATT_MEMBERS, $members);
        }else {
            
            if ($request->existInGET('option') && $request->getDataGET('option') == 'sponsorized') {
                $members = $this->memberDAOManager->findSponsorizedByMember($member->getId());
                $request->addAttribute(self::ATT_MEMBERS, $members);
            } else {
                //comptage des downlines
                $left = $this->memberDAOManager->countLeftChild($member->getId());
                $middle = $this->memberDAOManager->countMiddleChild($member->getId());
                $right = $this->memberDAOManager->countRightChild($member->getId());
                
                $request->addAttribute(self::LEFT_CHILDS, $left);
                $request->addAttribute(self::MIDDLE_CHILDS, $middle);
                $request->addAttribute(self::RIGHT_CHILDS, $right);
            }
            
        }
    }
    
    /**
     * panel des historique des retraits
     * @param Request $request
     * @param Response $response
     */
    public function executeWithdrawals (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Withdrawal Money");
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        
        $compte = $this->getAccount();
        
        if (!$compte->hasWithdrawRequest()) {
            $response->sendRedirect("/member/withdrawals/new.html");
        }
        
        $request->addAttribute(self::ATT_ACCOUNT, $compte);
        
        //retraits
        if ($this->withdrawalDAOManager->checkByMember($member->getId())) {
            $withdrawals = $this->withdrawalDAOManager->findByMember($member->getId());
        }else {
            $withdrawals = array();
        }
        
        $request->addAttribute(self::ATT_WITHDRAWELS, $withdrawals);
        
    }
    
    /**
     *  Nouveau Retrait
     * @param Request $request
     * @param Response $response
     */
    public function executeNewWithdrawal (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Withdrawal Money");

        $request->forward('alertMatching', $this->getModule());

        $account = $this->getAccount();
        $request->addAttribute(self::ATT_ACCOUNT, $account);

        if ($account->getMember()->getWithdrawalsRequest() != 0) {
            $response->sendRedirect('/member/withdrawals/');
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new WithdrawalFormValidator($this->getDaoManager());
            
            $withdrawel = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/withdrawals/");
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_WITHDRAWEL, $withdrawel);
        }
        
        $offices = $this->officeDAOManager->findByVisibility();

        $request->addAttribute(self::ATT_OFFICES, $offices);
        $request->addAttribute(self::ATT_MEMBER, $account->getMember());
    }

    public function executeAlertMatching (Request $request, Response $response) : void {

    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateWithdrawal (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        
        if (!$this->withdrawalDAOManager->checkById($id)) {
            $response->sendError("no data match at request URL");
        }
        
        $withdrawal = $this->withdrawalDAOManager->findById($id);
        if ($member->getId() != $withdrawal->getMember()->getId() || $withdrawal->getAdmin() != null) {
            $response->sendError("no data match at request URL");
        }
        
        //$response->sendRedirect("/member/withdrawals/");
        
        $account = $this->getAccount();
        $request->addAttribute(self::ATT_ACCOUNT, $account);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new WithdrawalFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, $id);
            $updatedWithdrawal = $form->updateAfterValidation($request);
            
            $withdrawal->setOffice($updatedWithdrawal->getOffice());
            $withdrawal->setTelephone($updatedWithdrawal->getTelephone());
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/withdrawals/");
            }
            
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_WITHDRAWEL, $withdrawal);
        $request->addAttribute(self::ATT_OFFICES, $this->officeDAOManager->findAll());
    }
    
    /**
     * affichage de la fiche de vente d'un membre
     * @param Request $request
     * @param Response $response
     */
    public function executeSellSheet (Request $request, Response $response) : void {
        /**
         * @var Member $member
         */
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        if($this->sellSheetRowDAOManager->checkByMember($member->getId())) {
            $sells =  $this->sellSheetRowDAOManager->findByMember($member->getId());
        } else {
            $sells = [];
        }

        $request->addAttribute(self::ATT_SELL_SHEETS, $sells);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeTree (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Tree");
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        
        if ($request->existInGET('foot')) {
            //chargement des downlines
            $members = [];
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $members[] = $this->memberDAOManager->findLeftDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countLeftChild($member->getId());
                }break;
                
                case 'middle' : {//middle
                    $members[] = $this->memberDAOManager->findMiddleDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countRightChild($member->getId());
                }break;
                
                case 'right' : {//right
                    $members[] = $this->memberDAOManager->findRightDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countMiddleChild($member->getId());
                }break;
                
                default : {//all Member
                    $members = $this->memberDAOManager->findDownlinesStacks($member->getId());
                    $count = $this->memberDAOManager->countChilds($member->getId());
                }
            }
            
            $request->addAttribute(self::PARAM_DOWNLINE_COUNT, $count);
            $request->addAttribute(self::ATT_MEMBERS, $members);
            
            $member->setChilds($members);
            $member->setParent(null);
            
            $formater = new TreeFormatter($member);
            $request->addAttribute(self::ATT_TREE_FORMATTER, $formater);            
        }
        
        //comptage des downlines
        $left = $this->memberDAOManager->countLeftChild($member->getId());
        $middle = $this->memberDAOManager->countMiddleChild($member->getId());
        $right = $this->memberDAOManager->countRightChild($member->getId());
        
        $request->addAttribute(self::LEFT_CHILDS, $left);
        $request->addAttribute(self::MIDDLE_CHILDS, $middle);
        $request->addAttribute(self::RIGHT_CHILDS, $right);
    }
    
    
    /**
     * consultation de l'historique des hoperation dans le compte d'un membre
     * @param Request $request
     * @param Response $response
     */
    public function executeHistory (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Account history");
        
        if ($request->existInGET('day')) {
            $dateMin = new \DateTime("{$request->getDataGET('year')}-{$request->getDataGET('month')}-{$request->getDataGET('day')}");
            $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
            $dateMax = null;
            $month->addSelectedDate($dateMin);
        } else {      
            
            if ($request->existInGET('month')) {
                $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
            }else{            
                $month = new Month();
            }
            $dateMin = $month->getFirstDay();
            $dateMax = $month->getLastDay();
        }
        
        
        $member = $request->getSession()->getAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION);
        
        if ($this->withdrawalDAOManager->checkHistoryByMember($member->getId(), $dateMin, $dateMax)) {
            $withdrawals = $this->withdrawalDAOManager->findHistoryByMember($member->getId(), $dateMin, $dateMax);
        }else {
            $withdrawals = array();
        }
        
        if ($this->pointValueDAOManager->checkHistoryByMember($member->getId(), $dateMin, $dateMax)) {
            $points = $this->pointValueDAOManager->findHistoryByMember($member->getId(), $dateMin, $dateMax);
        }else {
            $points = array();
        }
        
        if ($this->bonusGenerationDAOManager->checkHistoryByMember($member->getId(), $dateMin, $dateMax)) {
            $bonus = $this->bonusGenerationDAOManager->findHistoryByMember($member->getId(), $dateMin, $dateMax);
        }else {
            $bonus = array();
        }
        
        if ($this->purchaseBonusDAOManager->checkHistoryByMember($member->getId(), $dateMin, $dateMax)) {
            $purchase = $this->purchaseBonusDAOManager->findHistoryByMember($member->getId(), $dateMin, $dateMax);
        } else {
            $purchase = [];
        }
        
        $request->addAttribute(self::ATT_POINTS_VALUES, $points);
        $request->addAttribute(self::ATT_WITHDRAWELS, $withdrawals);
        $request->addAttribute(self::ATT_BONUS_GENERATIONS, $bonus);
        $request->addAttribute(self::ATT_PURCHASE_BONUS, $purchase);
        $request->addAttribute(self::ATT_MONTH, $month);
    }
}

