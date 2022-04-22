<?php
namespace Applications\Admin\Modules\Members;

use Applications\Admin\AdminController;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Validators\GradeMemberFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;
use Core\Shivalik\Validators\MemberFormValidator;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\ToastMessage;
use PHPBackend\Image2D\Mlm\TreeFormatter;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeBuilder;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeRender;

/**
 *
 * @author Esaie MHS
 *        
 */
class MembersController extends AdminController
{
    const ATT_SELECTED_ITEM_MENU = 'SELECTED_ITEM_MENU';
    const ATT_ITEM_MENU_DASHBORAD = 'ITEM_MENU_DASHBORAD';
    const ATT_ITEM_MENU_WITHDRAWALS = 'ITEM_MENU_WITHDRAWALS';
    const ATT_ITEM_MENU_DOWNLINES = 'ITEM_MENU_DOWNLINES';
    
    const CONFIG_MAX_MEMBER_VIEW_STEP = 'maxMembers';
    
    
    const ATT_COUNTRYS = 'countrys';    
    const ATT_LOCALISATION = 'localisation';
    const ATT_COMPTE = 'compte';
    const ATT_MEMBERS = 'members';
    const ATT_MEMBERS_REQUEST = 'membersRequest';
    const ATT_MEMBER = 'member';
    const ATT_GRADE_MEMBER = 'gradeMember';
    const ATT_REQUESTED_GRADE_MEMBER = 'RequestedGradeMember';
    const ATT_GRADES = 'grades';
    const ATT_SOLDE = 'solde';
    const ATT_SOLDE_WITHDRAWALS = 'soldeWithdrawals';
    const ATT_WITHDRAWALS = 'withdrawals';
    
    const LEFT_CHILDS = 'LEFT';
    const MIDDLE_CHILDS = 'MIDDLE';
    const RIGHT_CHILDS = 'RIGHT';
    
    const ATT_TREE_FORMATER = 'TREE_FORMATTER';
    
    
    /**
     * @var GradeDAOManager
     */
    private $gradeDAOManager;
    
    /**
     * @var CountryDAOManager
     */
    private $countryDAOManager;
    
    
    /**
     * @var GradeMemberDAOManager
     */
    private $gradeMemberDAOManager;
    
    /**
     * @var Member
     */
    private $member;//le compte du membre encours de consultation

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Http\HTTPController::init()
     */
    protected function init(Request $request, Response $response): void {
        parent::init($request, $response);
        
        if ($request->existInGET('id')) {
            $id = intval($request->getDataGET('id'), 10);
            if(!$this->memberDAOManager->checkById($id)){
                $response->sendError();
            }
            $this->member = $this->memberDAOManager->findById($id);
            $request->addAttribute(self::ATT_MEMBER, $this->member);
        }
        
        $nombre = $this->memberDAOManager->countAll();
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
        $request->addAttribute(self::ATT_VIEW_TITLE, "Union members");
    }

    /**
     * Pour effectuer une recherche dans la liste des membres du syndicat
     * la recherche s'effectuer sur:
     * -les noms
     * -le poseudo de connexion
     * -le matricule
     * @param Request $request
     * @param Response $response
     */
    public function executeSearch (Request $request, Response $response) : void {
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new MemberFormValidator($this->getDaoManager());
            $members = $form->searchAfterValidation($request);
            foreach ($members as $member) {
                $member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($member->getId()));
            }
            $request->addAttribute(self::ATT_FORM_VALIDATOR, $form->toFeedback());
            $request->addAttribute(self::ATT_MEMBERS, $members);
        }
    }

    /** consulter la liste des membres du systeme
     * @param Request $request
     * @param Response $response
     */
    public function executeMembers (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST) {//RECHERCHE D'UN MEMBRE EN FONCTION DES SON ID
            if ($this->memberDAOManager->checkByMatricule($request->getDataPOST('id'))) {
                $member = $this->memberDAOManager->findByMatricule($request->getDataPOST('id'));
                $response->sendRedirect("/admin/members/{$member->getId()}/");
            }
            
            $message = new ToastMessage('Error', "Know user ID in system => {$request->getDataPOST('id')}", ToastMessage::MESSAGE_ERROR);
            $request->addToast($message);
            $response->sendRedirect('/admin/');
        }
        
        if ($this->gradeMemberDAOManager->checkRequest()) {
        	$requestMembers = $this->gradeMemberDAOManager->findAllRequest();
        }else {
        	$requestMembers = array();
        }
        
        $nombre = $this->memberDAOManager->countAll();
        if ($nombre>0) {
            if ($request->existInGET('limit')) {
                $offset = intval($request->getDataGET('offset'), 10);
                $limit = intval($request->getDataGET('limit'), 10);
                $members = $this->memberDAOManager->findAll($limit, $offset);
            } else {
                $limit = intval($request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)!=null? $request->getApplication()->getConfig()->get(self::CONFIG_MAX_MEMBER_VIEW_STEP)->getValue() : 50);
                $members = $this->memberDAOManager->findAll($limit, 0);
            }
        }else {
            $members = array();
        }
        
        /**
         * @var Member $member
         */
        foreach ($members as $member) {
            if($this->gradeMemberDAOManager->checkCurrentByMember($member->getId()))
                $member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($member->getId()));
        }
        
        $request->addAttribute(self::ATT_MEMBERS, $members);
        $request->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
        $request->addAttribute(self::ATT_MEMBERS_REQUEST, $requestMembers);
    }
    
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeAddMember (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GradeMemberFormValidator($this->getDaoManager());
            $request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $this->getConnectedAdmin());
            $gm = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/members/");
            }
            
            $request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
            $request->addAttribute(MemberFormValidator::MEMBER_FEEDBACK, $form->getFeedback(MemberFormValidator::MEMBER_FEEDBACK));
            $form->includeFeedback($request);
            
            $request->addAttribute(self::ATT_GRADE_MEMBER, $gm);
            $request->addAttribute(self::ATT_MEMBER, $gm->getMember());
            $request->addAttribute(self::ATT_LOCALISATION, $gm->getMember()->getLocalisation());
        }
        
        //
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
        $request->addAttribute(self::ATT_GRADES, $this->gradeDAOManager->findAll());
    }
    
    
    /**
     * Dashoard du compte d'un membre
     * @param Request $request
     * @param Response $response
     */
    public function executeMember (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DASHBORAD);
        
        $this->member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($this->member->getId()));
        
        if ($this->gradeMemberDAOManager->checkCurrentByMember($this->member->getId())) {
            $gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($this->member->getId());
            $gradeMember->setMember($this->member);
	        $request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
        }
        
        if ($this->gradeMemberDAOManager->checkRequestedByMember($this->member->getId())) {
            $requestedGradeMember = $this->gradeMemberDAOManager->findRequestedByMember($this->member->getId());
            $requestedGradeMember->setMember($this->member);
            $request->addAttribute(self::ATT_REQUESTED_GRADE_MEMBER, $requestedGradeMember);
        }
        
        $compte = $this->memberDAOManager->loadAccount($this->member);
        $request->addAttribute(self::ATT_COMPTE, $compte);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateMember (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST) {
        	$form = new MemberFormValidator($this->getDaoManager());
        	$request->addAttribute($form::CHAMP_ID, $this->member->getId());
        	$member = $form->updateAfterValidation($request);
        	if (!$form->hasError()) {
        		$response->sendRedirect("/admin/members/{$this->member->getId()}/");
        	}
        	$form->includeFeedback($request);
        	$request->addAttribute($form::MEMBER_FEEDBACK, $form->toFeedback());
        }
        
        $compte = $this->memberDAOManager->loadAccount($member);
        
        $request->addAttribute(self::ATT_COMPTE, $compte);
        $request->addAttribute(self::ATT_MEMBER, $member);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeResetPassword (Request $request, Response $response) : void {
    	
    	if ($request->getMethod() == Request::HTTP_POST) {
    		
    		$form = new MemberFormValidator($this->getDaoManager());
    		$request->addAttribute($form::CHAMP_ID, $this->member->getId());
    		$form->resetPasswordAfterValidation($request);
    		
    		if (!$form->hasError()) {
    			$response->sendRedirect("/admin/members/{$this->member->getId()}/");
    		}
    		
    		$form->includeFeedback($request);
    	}
    	
    	$compte = $this->memberDAOManager->loadAccount($this->member);
    	$request->addAttribute(self::ATT_COMPTE, $compte);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeDownlines (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);        
        
        if ($request->existInGET('foot')) {
            //chargement des downlines
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $members = $this->memberDAOManager->findLeftDownlinesChilds($this->member->getId());
                }break;
                
                case 'middle' : {//middle
                    $members = $this->memberDAOManager->findMiddleDownlinesChilds($this->member->getId());
                }break;
                
                case 'right' : {//right
                    $members = $this->memberDAOManager->findRightDownlinesChilds($this->member->getId());
                }break;
                
                default : {//all Member
                    $members = $this->memberDAOManager->findDownlinesChilds($this->member->getId());
                }
            }
            
            $request->addAttribute(self::ATT_MEMBERS, $members);
        }else {
            //comptage des downlines
            $left = $this->memberDAOManager->countLeftChild($this->member->getId());
            $middle = $this->memberDAOManager->countMiddleChild($this->member->getId());
            $right = $this->memberDAOManager->countRightChild($this->member->getId());
            
            $request->addAttribute(self::LEFT_CHILDS, $left);
            $request->addAttribute(self::MIDDLE_CHILDS, $middle);
            $request->addAttribute(self::RIGHT_CHILDS, $right);
        }
        
        $account = $this->memberDAOManager->loadAccount($member);
        $request->addAttribute(self::ATT_COMPTE, $account);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeDownlinesHierarchy (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);
        
        $member = $this->member;
        
        if ($request->existInGET('foot')) {
            //chargement des downlines
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::LEFT_FOOT);
                }break;
                
                case 'middle' : {//middle
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::MIDDEL_FOOT);
                }break;
                
                case 'right' : {//right
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::RIGHT_FOOT);
                }break;
                
                default : {//all Member
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId());
                }
            }
            
        }else {
            $childs = $this->memberDAOManager->findDownlinesStacks($member->getId());
        }
        
        $member->setChilds($childs);
        $member->setParent(null);
        
        $formater = new TreeFormatter($member);
        $account = $this->memberDAOManager->loadAccount($member);
        $account->calcul();
        $request->addAttribute(self::ATT_TREE_FORMATER, $formater);
        $request->addAttribute(self::ATT_COMPTE, $account);
        $request->addAttribute(self::ATT_MEMBER, $member);
    }
    
    
    /**
     * generation de l'arbre genealogique d'un membre
     * @param Request $request
     * @param Response $response
     */
    public function executeTree (Request $request, Response $response) : void {
        $response->sendRedirect("/admin/members/{$this->member->getId()}/");
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);
        
        $member = $this->member;
        
        
        if ($request->existInGET('foot')) {
            //chargement des downlines
            switch ($request->getDataGET('foot')){
                case 'left' : {//left
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::LEFT_FOOT);
                }break;
                
                case 'middle' : {//middle
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::MIDDEL_FOOT);
                }break;
                
                case 'right' : {//right
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId(), Member::RIGHT_FOOT);
                }break;
                
                default : {//all Member
                    $childs = $this->memberDAOManager->findDownlinesStacks($member->getId());
                }
            }
            
        }else {
            $childs = $this->memberDAOManager->findDownlinesStacks($member->getId());
        }
        
        $member->setChilds($childs);
        $member->setParent(null);
        
        $builder = new TernaryTreeBuilder($member, 100);
        $render = new TernaryTreeRender($builder);
        
        $render->render();
        $account = $this->memberDAOManager->loadAccount($member);
        $account->calcul();
        $request->addAttribute(self::ATT_COMPTE, $account);
        $request->addAttribute(self::ATT_MEMBER, $member);
    }
    
    
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeWithdrawalsMember (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_WITHDRAWALS);

        $member = $this->member;
        
        if ($request->existInGET('requestId')) {
            $this->withdrawalDAOManager->validate(intval($request->getDataGET('requestId')), $this->getConnectedAdmin()->getId());
        }
        
        
        if ($this->gradeMemberDAOManager->checkCurrentByMember($member->getId())) {
            $gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($member->getId());
            $gradeMember->setMember($member);
            $request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
        }
        
        if ($this->gradeMemberDAOManager->checkRequestedByMember($member->getId())) {
            $requestedGradeMember = $this->gradeMemberDAOManager->findRequestedByMember($member->getId());
            $requestedGradeMember->setMember($member);
            $request->addAttribute(self::ATT_REQUESTED_GRADE_MEMBER, $requestedGradeMember);
        }
        
        //Chargement des PV;
        $compte = $this->memberDAOManager->loadAccount($member);
        
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
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeStateMember (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Union members");
        $state = ($request->getDataGET('state') == 'enable');

        $member = $this->member;
        
        if ($state != $member->isEnable()) {
            $this->memberDAOManager->updateState($member->getId(), $state);
        }
        
        $response->sendRedirect("/admin/members/{$member->getId()}/");
        
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpgradeMember (Request $request, Response $response) : void {

        if ($this->gradeMemberDAOManager->checkRequest($$this->member->getId())) {
            $response->sendRedirect("/admin/members/{$this->member->getId()}/");
        }

        $member = $this->member;
        $gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($member->getId());
        $gradeMember->setMember($member);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GradeMemberFormValidator($this->getDaoManager());
            $request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $this->getConnectedAdmin());
            $request->addAttribute($form::FIELD_MEMBER, $member->getId());
            $gradeMember = $form->upgradeAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/members/{$member->getId()}/");
            }
            
            $request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
            $form->includeFeedback($request);
        }

        $request->addAttribute(self::ATT_GRADE_MEMBER, $gradeMember);
        $grades = $this->gradeDAOManager->findAll();
        $request->addAttribute(self::ATT_GRADES, $grades);
    }
    
    
    /**
     *
     * @param Request $request
     * @param Response $response
     */
    public function executeCertifyMember (Request $request, Response $response) : void {
        $gmId = intval($request->getDataGET('idGradeMember'), 10);
        
        if (!$this->gradeMemberDAOManager->checkById($gmId)) {
            $response->sendError();
        }
        
        /**
         * @var GradeMember $gradeMember
         */
        $gradeMember = $this->gradeMemberDAOManager->findById($gmId);
        $member = $gradeMember->getMember();
        
        if ($gradeMember->isEnable()) {
            $response->sendError("impossible to share the packs because the operation is already done, and this operation is irreversible");
        }
        
        //Activation du comote
        $form = new GradeMemberFormValidator($this->getDaoManager());
        $request->addAttribute($form::CHAMP_ID, $gmId);
        $form->enableAfterValidation($request);
        
        $request->addToast($form->buildToastMessage());
        $response->sendRedirect("/admin/members/");
    }
    
    
    /**
     * Visualisation des uplines du compte d'un membre
     * @param Request $request
     * @param Response $response
     */
    public function executeUplinesMember (Request $request, Response $response) : void {
        
        if(!$this->memberDAOManager->checkParent($this->member->getId()) || !$this->memberDAOManager->checkSponsor($this->member->getId())) {
            $response->sendRedirect("/admin/members/{$this->member->getId()}/");
        }
        
        $this->member->setSponsor($this->memberDAOManager->findById($this->member->getSponsor()->getId()));
        $this->member->getSponsor()->setPacket($this->gradeMemberDAOManager->findCurrentByMember($this->member->getSponsor()->getId()));

        $this->member->setParent($this->memberDAOManager->findById($this->member->getParent()->getId()));
        $this->member->getParent()->setPacket($this->gradeMemberDAOManager->findCurrentByMember($this->member->getParent()->getId()));
        
    }
}

