<?php
namespace Applications\Admin\Modules\Members;

use Applications\Admin\AdminController;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Validators\GradeMemberFormValidator;
use Core\Shivalik\Entities\Member;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeBuilder;
use PHPBackend\Image2D\Mlm\Ternary\TernaryTreeRender;
use PHPBackend\Image2D\Mlm\TreeFormatter;
use Core\Shivalik\Validators\MemberFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;
use PHPBackend\ToastMessage;

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
     * {@inheritDoc}
     * @see \Applications\Admin\AdminController::__construct()
     */
    public function __construct(Application $application, string $module, string $action)
    {
        parent::__construct($application, $module, $action);
        $nombre = $this->memberDAOManager->countAll();
        $application->getRequest()->addAttribute(self::PARAM_MEMBER_COUNT, $nombre);
        $application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Union members");
    }

    /**
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
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DASHBORAD);
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        $member->setPacket($this->gradeMemberDAOManager->findCurrentByMember($member->getId()));
        
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
        
        $compte = $this->memberDAOManager->loadAccount($member);

        $request->addAttribute(self::ATT_COMPTE, $compte);
        $request->addAttribute(self::ATT_MEMBER, $member);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateMember (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        
        if ($request->getMethod() == Request::HTTP_POST) {
        	$form = new MemberFormValidator($this->getDaoManager());
        	$request->addAttribute($form::CHAMP_ID, $id);
        	$member = $form->updateAfterValidation($request);
        	if (!$form->hasError()) {
        		$response->sendRedirect("/admin/members/{$id}/");
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
    	$id = intval($request->getDataGET('id'), 10);
    	if (!$this->memberDAOManager->checkById($id)) {
    		$response->sendError();
    	}
    	
    	/**
    	 * @var Member $member
    	 */
    	$member = $this->memberDAOManager->findById($id);
    	
    	if ($request->getMethod() == Request::HTTP_POST) {
    		$id = intval($request->getDataGET('id'), 10);
    		
    		$form = new MemberFormValidator($this->getDaoManager());
    		$request->addAttribute($form::CHAMP_ID, $id);
    		$form->resetPasswordAfterValidation($request);
    		
    		if (!$form->hasError()) {
    			$response->sendRedirect("/admin/members/{$id}/");
    		}
    		
    		$form->includeFeedback($request);
    	}
    	
    	$compte = $this->memberDAOManager->loadAccount($member);
    	
    	
    	
    	$request->addAttribute(self::ATT_COMPTE, $compte);
    	$request->addAttribute(self::ATT_MEMBER, $member);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeDownlines (Request $request, Response $response) : void {
        
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);
        
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
        
        $account = $this->memberDAOManager->loadAccount($member);
        $request->addAttribute(self::ATT_COMPTE, $account);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeDownlinesHierarchy (Request $request, Response $response) : void {
        
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        
        
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
        
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $response->sendRedirect("/admin/members/{$id}/");
        
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_DOWNLINES);
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        
        
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
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $request->addAttribute(self::ATT_SELECTED_ITEM_MENU, self::ATT_ITEM_MENU_WITHDRAWALS);
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        
        if ($request->existInGET('requestId')) {
            $this->withdrawalDAOManager->validate(intval($request->getDataGET('requestId')), $this->getConnectedAdmin()->getId());
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
        $compte = $this->memberDAOManager->loadAccount($member);
        
        if ($this->withdrawalDAOManager->checkByMember($member->getId())) {
            $withdrawals = $this->withdrawalDAOManager->checkByMember($member->getId());
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
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        $state = ($request->getDataGET('state') == 'enable');
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        
        if ($state != $member->isEnable()) {
            $this->memberDAOManager->updateState($id, $state);
        }
        
        $response->sendRedirect("/admin/members/{$id}/");
        
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpgradeMember (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        if (!$this->memberDAOManager->checkById($id)) {
            $response->sendError();
        }
        
        if ($this->gradeMemberDAOManager->checkRequest($id)) {
            $response->sendRedirect("/admin/members/{$id}/");
        }
        
        /**
         * @var Member $member
         */
        $member = $this->memberDAOManager->findById($id);
        $gradeMember = $this->gradeMemberDAOManager->findCurrentByMember($id);
        $gradeMember->setMember($member);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GradeMemberFormValidator($this->getDaoManager());
            $request->addAttribute(GradeMemberFormValidator::FIELD_OFFICE_ADMIN, $this->getConnectedAdmin());
            $request->addAttribute($form::FIELD_MEMBER, $member->getId());
            $gradeMember = $form->upgradeAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/admin/members/{$id}/");
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
     *
     * @param Request $request
     * @param Response $response
     */
    public function executeCertifyMember (Request $request, Response $response) : void {
        
        $id = intval($request->getDataGET('id'), 10);
        $gmId = intval($request->getDataGET('idGradeMember'), 10);
        
        if (!$this->memberDAOManager->checkById($id) || !$this->gradeMemberDAOManager->checkById($gmId)) {
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
        
        $request->addToast($form->buildAppMessage());
        $response->sendRedirect("/admin/members/");
    }
}

