<?php
namespace Applications\Member\Modules\Account;

use Applications\Member\MemberApplication;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Managers\BonusGenerationDAOManager;
use Core\Shivalik\Managers\OfficeBonusDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Application;
use Core\Shivalik\Entities\Account;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Calendar\Month;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Validators\WithdrawalFormValidator;
use PHPBackend\Image2D\Mlm\TreeFormatter;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeBuilder;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeRender;

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
    
    const LEFT_CHILDS = 'LEFT';
    const MIDDLE_CHILDS = 'MIDDLE';
    const RIGHT_CHILDS = 'RIGHT';
    
    const PARAM_DOWNLINE_COUNT = 'PARAM_DOWNLINE_COUNT';
    const ATT_TREE_FORMATTER = 'TREE_FORMATTER';
    
    const ATT_MONTH = 'month';
    const ATT_BONUS_GENERATIONS = 'bonusGeneration';
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
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    
    /**
     * {@inheritDoc}
     * @see HTTPController::__construct()
     */
    public function __construct(Application $application, string $action, string $module)
    {
        parent::__construct($application, $action, $module);
        $application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Account");
    }
    
    /**
     * @return Account
     */
    private function getAccount () : Account {
        $member = MemberApplication::getConnectedMember();
        return $this->memberDAOManager->getAccount($member);
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
        $member = MemberApplication::getConnectedMember();
        $gradeMember = $this->gradeMemberDAOManager->getCurrent($member->getId());
        $gradeMember->setMember($member);
        

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
        $member = MemberApplication::getConnectedMember();
        
        
        if ($request->existGET('foot')) {
            //chargement des downlines
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $members = $this->memberDAOManager->getLeftDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countLeftChild($member->getId());
                }break;
                
                case 'middle' : {//middle
                    $members = $this->memberDAOManager->getMiddleDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countRightChild($member->getId());
                }break;
                
                case 'right' : {//right
                    $members = $this->memberDAOManager->getRightDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countMiddleChild($member->getId());
                }break;
                
                default : {//all Member
                    $members = $this->memberDAOManager->getDownlinesChilds($member->getId());
                    $count = $this->memberDAOManager->countChilds($member->getId());
                }
            }
            $request->addAttribute(self::PARAM_DOWNLINE_COUNT, $count);
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
    }
    
    /**
     * panel des historique des retraits
     * @param Request $request
     * @param Response $response
     */
    public function executeWithdrawals (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Withdrawal Money");
        $member = MemberApplication::getConnectedMember();
        
        $compte = $this->getAccount();
        
        if (!$compte->hasWithdrawRequest()) {
            $response->sendRedirect("/member/withdrawals/new.html");
        }
        
        $request->addAttribute(self::ATT_ACCOUNT, $compte);
        
        //retraits
        if ($this->withdrawalDAOManager->hasOperation($member->getId())) {
            $withdrawals = $this->withdrawalDAOManager->forMember($member->getId());
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
        $account = $this->getAccount();
        $request->addAttribute(self::ATT_ACCOUNT, $account);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new WithdrawalFormValidator($this->getDaoManager());
            
            $withdrawel = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/withdrawals/");
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_WITHDRAWEL, $withdrawel);
        }
        
        $request->addAttribute(self::ATT_OFFICES, $this->officeDAOManager->getAll());
        $request->addAttribute(self::ATT_MEMBER, MemberApplication::getConnectedMember());
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateWithdrawal (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        $member = MemberApplication::getConnectedMember();
        
        if (!$this->withdrawalDAOManager->idExist($id)) {
            $response->sendError("no data match at request URL");
        }
        
        $withdrawal = $this->withdrawalDAOManager->getForId($id);
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
        $request->addAttribute(self::ATT_OFFICES, $this->officeDAOManager->getAll());
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeTransfer (Request $request, Response $response) : void {
        
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeTree (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Tree");
        $member = MemberApplication::getConnectedMember();
        
        
        if ($request->existGET('foot')) {
            //chargement des downlines
            $members = [];
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $members[] = $this->memberDAOManager->getLeftDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countLeftChild($member->getId());
                }break;
                
                case 'middle' : {//middle
                    $members[] = $this->memberDAOManager->getMiddleDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countRightChild($member->getId());
                }break;
                
                case 'right' : {//right
                    $members[] = $this->memberDAOManager->getRightDownlineStack($member->getId());
                    $count = $this->memberDAOManager->countMiddleChild($member->getId());
                }break;
                
                default : {//all Member
                    $members = $this->memberDAOManager->getDownlinesStacks($member->getId());
                    $count = $this->memberDAOManager->countChilds($member->getId());
                }
            }
            
            $request->addAttribute(self::PARAM_DOWNLINE_COUNT, $count);
            $request->addAttribute(self::ATT_MEMBERS, $members);
            
            $member->setChilds($members);
            $member->setParent(null);
            
            if ($request->getDataGET('affichage') == 'stack') {
                $formater = new TreeFormatter($member);
                $request->addAttribute(self::ATT_TREE_FORMATTER, $formater);
            }else{
                $response->sendRedirect("/member/tree/{$request->getDataGET('foot')}-stack.html");
                $builder = new TernaryTreeBuilder($member, 200);
                $render = new TernaryTreeRender($builder);
                
                $render->render();
            }
            
        }else {
            
            //comptage des downlines
            $left = $this->memberDAOManager->countLeftChild($member->getId());
            $middle = $this->memberDAOManager->countMiddleChild($member->getId());
            $right = $this->memberDAOManager->countRightChild($member->getId());
            
            $request->addAttribute(self::LEFT_CHILDS, $left);
            $request->addAttribute(self::MIDDLE_CHILDS, $middle);
            $request->addAttribute(self::RIGHT_CHILDS, $right);
        }
    }
    
    public function executeHistory (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Account history");
        
        if ($request->existGET('day')) {
            $dateMin = new \DateTime("{$request->getDataGET('year')}-{$request->getDataGET('month')}-{$request->getDataGET('day')}");
            $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
            $dateMax = null;
            $month->addSelectedDate($dateMin);
        } else {      
            
            if ($request->existGET('month')) {
                $month = new Month(intval($request->getDataGET('month'), 10), intval($request->getDataGET('year'), 10));
            }else{            
                $month = new Month();
            }
            $dateMin = $month->getFirstDay();
            $dateMax = $month->getLastDay();
        }
        
        
        $member = MemberApplication::getConnectedMember();
        
        if ($this->withdrawalDAOManager->hasCreationHistory($dateMin, $dateMax, array('member' => $member->getId()))) {
            $withdrawals = $this->withdrawalDAOManager->getCreationHistory($dateMin, $dateMax, array('member' => $member->getId()));
        }else {
            $withdrawals = array();
        }
        
        if ($this->pointValueDAOManager->hasCreationHistory($dateMin, $dateMax, array('member' => $member->getId()))) {
            $points = $this->pointValueDAOManager->getCreationHistory($dateMin, $dateMax, array('member' => $member->getId()));
        }else {
            $points = array();
        }
        
        if ($this->bonusGenerationDAOManager->hasCreationHistory($dateMin, $dateMax, array('member' => $member->getId()))) {
            $bonus = $this->bonusGenerationDAOManager->getCreationHistory($dateMin, $dateMax, array('member' => $member->getId()));
        }else {
            $bonus = array();
        }
        
        $request->addAttribute(self::ATT_POINTS_VALUES, $points);
        $request->addAttribute(self::ATT_WITHDRAWELS, $withdrawals);
        $request->addAttribute(self::ATT_BONUS_GENERATIONS, $bonus);
        $request->addAttribute(self::ATT_MONTH, $month);
    }
}

