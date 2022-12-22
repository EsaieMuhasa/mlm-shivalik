<?php
namespace Applications\Root\Modules\Settings;

use Core\Shivalik\Filters\SessionRootFilter;
use Core\Shivalik\Managers\CountryDAOManager;
use Core\Shivalik\Managers\GenerationDAOManager;
use Core\Shivalik\Managers\GradeDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\OfficeAdminDAOManager;
use Core\Shivalik\Managers\OfficeDAOManager;
use Core\Shivalik\Managers\SizeDAOManager;
use Core\Shivalik\Validators\CountryFormValidator;
use Core\Shivalik\Validators\GenerationFormValidator;
use Core\Shivalik\Validators\GradeFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;
use Core\Shivalik\Validators\MemberFormValidator;
use Core\Shivalik\Validators\OfficeAdminFormValidator;
use Core\Shivalik\Validators\OfficeFormValidator;
use Core\Shivalik\Validators\SizeFormValidator;
use PHPBackend\Application;
use PHPBackend\Request;
use PHPBackend\Response;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\GradeMember;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Managers\VirtualMoneyDAOManager;
use Core\Shivalik\Managers\GradeMemberDAOManager;
use Core\Shivalik\Entities\MoneyGradeMember;

/**
 *
 * @author Esaie MHS
 *        
 */
class SettingsController extends HTTPController
{
    
    const ATT_ADMIN = 'admin';
    const ATT_ADMINS = 'admins';
    
    const ATT_GRADE = 'grade';
    const ATT_GRADES = 'grades';
    
    const ATT_GENERATION = 'generation';
    const ATT_GENERATIONS = 'generations';
    
    const ATT_OFFICE = 'office';
    const ATT_OFFICES = 'offices';
    
    const ATT_SIZE = 'size';
    const ATT_SIZES = 'sizes';
    
    const ATT_COUNTRYS = 'countrys';
    const ATT_COUNTRY = 'country';
    
    const ATT_LOCALISATION = 'localisation';
    const ATT_GRADE_MEMBER = 'gradeMember';
    const ATT_MEMBER = 'member';
    
    
    /**
     * @var OfficeAdminDAOManager
     */
    private $officeAdminDAOManager;
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /**
     * @var GenerationDAOManager
     */
    private $generationDAOManager;
    
    /**
     * @var GradeDAOManager
     */
    private $gradeDAOManager;
    
    /**
     * @var CountryDAOManager
     */
    private $countryDAOManager;
    
    /**
     * @var SizeDAOManager
     */
    private $sizeDAOManager;
    
    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;
    
    /**
	 * {@inheritDoc}
	 * @see \PHPBackend\Http\HTTPController::__construct()
	 */
	public function __construct(Application $application, $module, $action) {
		parent::__construct($application, $module, $action);
		$application->getRequest()->addAttribute(self::ATT_VIEW_TITLE, "Settings");
	}

	/**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeLogin (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST){
            $pseudo = $request->getDataPOST('pseudo');
            $password = $request->getDataPOST('password');
            $errors = [];
            
            $root = $this->getApplication()->getConfig()->getUser($pseudo);
            if ($root!=null) {
                if ($root->getPassword() != $password) {
                    $errors['message'] = "Invalid password";
                }
            }else {
                $errors['message'] = "invalid username";
            }
            
            if (empty($errors)) {
                $request->getSession()->addAttribute(SessionRootFilter::ROOT_CONNECTED_SESSION, $root);
                $response->sendRedirect($request->getURI());
            }else {
                $request->addAttribute('user', $root);
                $request->addAttribute('errors', $errors);
                $request->addAttribute('result', 'Connection failure');
            }
        }
    }
    
    /**
     * Connexion de root a une session X
     * @param Request $request
     * @param Response $response
     */
    public function executeLoginAdmin (Request $request, Response $response) : void {
        $id = intval($request->getDataGET('id'), 10);
        $user = $request->getDataGET('session');
        
        switch ($user) {
            case 'office' : {
                /**
                 * @var OfficeAdmin $officeAdmin
                 */
                $officeAdmin = $this->officeAdminDAOManager->findById($id);
                $officeAdmin->setOffice($this->officeDAOManager->findById($officeAdmin->getOffice()->getId()));
                if ($officeAdmin->getOffice()->isCentral()) {
                    $user = 'admin';
                    $request->getSession()->addAttribute(SessionAdminFilter::ADMIN_CONNECTED_SESSION, $officeAdmin);
                } else {                    
                    $request->getSession()->addAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION, $officeAdmin);
                }
            } break;
            case 'member' : {
                $member = $this->memberDAOManager->findById($id);
                if ($this->officeDAOManager->checkByMember($member->getId())) {//our les utilisateur qui ont des bureau
                    $office = $this->officeDAOManager->findByMember($member->getId());
                    $member->setOfficeAccount($office);
                }
                $request->getSession()->addAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION, $member);
            } break;
        }
        
        $response->sendRedirect("/{$user}/");
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        
        if ($request->getDataGET('member')) {
            $id = intval($request->getDataGET('member'), 10);
            $parent = intval($request->getDataGET('parent'), 10);
            
            $this->memberDAOManager->changeParentByMember($id, $parent);
        }
    }
    
    /**
     * insersion d'un membre dans l'arbre
     * @param Request $request
     * @param Response $response
     */
    public function executeInsertMember (Request $request, Response $response) : void {
        if ($request->getMethod() == Request::HTTP_POST) {

            $form = new MemberFormValidator($this->getDaoManager());
            $gm = $form->insertBelowAferValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/root/");
            }
            
            $request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
            $request->addAttribute(MemberFormValidator::MEMBER_FEEDBACK, $form->getFeedback(MemberFormValidator::MEMBER_FEEDBACK));
            $form->includeFeedback($request);
            
            $request->addAttribute(self::ATT_GRADE_MEMBER, $gm);
            $request->addAttribute(self::ATT_MEMBER, $gm->getMember());
            $request->addAttribute(self::ATT_LOCALISATION, $gm->getMember()->getLocalisation());
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
        $grades = $this->gradeDAOManager->findAll();
        $request->addAttribute(self::ATT_GRADES, $grades);
    }


    /**
     * migration d'un compte d'un membre, d'un reseau A a un reseau B
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeMigrateToNetwork (Request $request, Response $response) : void {

        // $response->sendError("Reserved to Ing Esaie MUHASA only");

        $nodeId = @intval($request->getDataGET('nodeId'), 10);
        $sponsorId = @intval($request->getDataGET('sponsorId'), 10);

        $node = $this->memberDAOManager->findById($nodeId);
        $sponsor = $this->memberDAOManager->findById($sponsorId);

        $this->memberDAOManager->migrateToNetwork($node, $sponsor, $sponsor);


        $response->sendRedirect('/root/');

    }


    /**
     * action de regeneration des points valeurs des downlines d'une compte X
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function executeRegeneratePointsByDownlines(Request $request, Response $response): void
    {
        $nodeKey = $request->getDataGET('id');
        $member = $this->memberDAOManager->findById(intval($nodeKey));
        $this->memberDAOManager->regeneratePointsByDownlines($member);
        
        $response->sendRedirect('/root/');
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeAdmins (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        if ($this->officeAdminDAOManager->countAll() > 0) {
            $request->addAttribute(self::ATT_ADMINS, $this->officeAdminDAOManager->findAll());
        }else {
            $request->addAttribute(self::ATT_ADMINS, array());
        }
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeAddAdmin (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $admin = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/admins/');
            }
            $request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
            $request->addAttribute(self::ATT_LOCALISATION, $admin->getLocalisation());
            $request->addAttribute(self::ATT_ADMIN, $admin);
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
        $request->addAttribute(self::ATT_OFFICES, $this->officeDAOManager->findAll());
    }
    
    
    /**
     * to update the admin acount
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateAdmin (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        
        $id = intval($request->getDataGET('id'), 10);
        if ($this->officeAdminDAOManager->checkById($id)) {
            $admin = $this->officeAdminDAOManager->findById($id);
        } else {
            $response->sendError();
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $admin = $form->updateAfterValidation($request);
            if (!$form->hasError()) {
                $response->sendRedirect('/root/admins/');
            }
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_ADMIN, $admin);
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
    }
    
    
    /***
     * process action to show all grade in database
     * @param Request $request
     * @param Response $response
     */
    public function executeGrades (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        if ($this->gradeDAOManager->countAll() > 0) {
            $request->addAttribute(self::ATT_GRADES, $this->gradeDAOManager->findAll());
        }else {
            $request->addAttribute(self::ATT_GRADES, array());
        }
        
    }
    
    
    /**
     * process action to create a new grade
     * @param Request $request
     * @param Response $response
     */
    public function executeAddGrade (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        if ($this->generationDAOManager->countAll()>=0) {
            $generations = $this->generationDAOManager->findAll();
        }else {
            $generations = array();
        }
        
        $request->addAttribute(self::ATT_GENERATIONS, $generations);
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GradeFormValidator($this->getDaoManager());
            $grade = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/grades/');
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_GRADE, $grade);
        }
    }
    
    /**
     * update a new grade
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateGrade (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        
        if ($this->gradeDAOManager->checkById($id)) {
            $grade = $this->gradeDAOManager->findById($id);
            if ($this->generationDAOManager->countAll()>=0) {
                $generations = $this->generationDAOManager->findAll();
            }else {
                $generations = array();
            }
            $request->addAttribute(self::ATT_GENERATIONS, $generations);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GradeFormValidator($this->getDaoManager());
            $grade = $form->updateAfterValidation($request);
            if (!$form->hasError()) {
                $response->sendRedirect('/root/grades/');
            }
            $form->includeFeedback($request);
        }
        $request->addAttribute(self::ATT_GRADE, $grade);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeGenerations (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        if ($this->generationDAOManager->countAll() > 0){
            $generations = $this->generationDAOManager->findAll();
        }else {
            $generations = array();
        }
        
        $request->addAttribute(self::ATT_GENERATIONS, $generations);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeCountrys (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        if ($this->countryDAOManager->countAll() > 0) {
            $countrys = $this->countryDAOManager->findAll();
        }else {
            $countrys = array();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $countrys);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeAddCountry (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new CountryFormValidator($this->getDaoManager());
            $country= $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/root/countrys/");
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_COUNTRY, $country);
        }
    }
    
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateCountry (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        if ($this->countryDAOManager->checkById($id)) {
            $country = $this->countryDAOManager->findById($id);
        } else {
            $response->sendError();
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new CountryFormValidator($this->getDaoManager());
            $country= $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/root/countrys/");
            }
            
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_COUNTRY, $country);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeAddGeneration (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        if ($request->getMethod() == Request::HTTP_POST) {
            
            $form = new GenerationFormValidator($this->getDaoManager());
            
            $generation = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/generations/');
            }
            
            $request->addAttribute(self::ATT_GENERATION, $generation);
            $form->includeFeedback($request);
        }
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateGeneration (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        $id = intval($request->getDataGET('id'));
        
        if ($this->generationDAOManager->checkById($id)) {
            $generation = $this->generationDAOManager->findById($id);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new GenerationFormValidator($this->getDaoManager());
            $generation = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/generations/');
            }
            
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_GENERATION, $generation);
        
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeOffices (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        if ($this->officeDAOManager->countAll() > 0){
            $offices = $this->officeDAOManager->findAll();
        }else {
            $offices = array();
        }
        
        $request->addAttribute(self::ATT_OFFICES, $offices);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeAddOffice (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeFormValidator($this->getDaoManager());
            
            $office = $form->createAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/offices/');
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(LocalisationFormValidator::LOCALISATION_FEEDBACK, $form->getFeedback(LocalisationFormValidator::LOCALISATION_FEEDBACK));
            $request->addAttribute(self::ATT_LOCALISATION, $office->getLocalisation());
            $request->addAttribute(self::ATT_OFFICE, $office);
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeSizes(Request $request, Response $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	
    	if ($this->sizeDAOManager->countAll() <= 0){
    		$sizes = array();
    	}else {
    		$sizes = $this->sizeDAOManager->findAll();
    	}
    	
    	$request->addAttribute(self::ATT_SIZES, $sizes);
    }
    
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeAddSize(Request $request, Response $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	if ($request->getMethod() == Request::HTTP_POST) {
    		$form = new SizeFormValidator($this->getDaoManager());
    		$size = $form->createAfterValidation($request);
    		
    		if (!$form->hasError()) {
    			$response->sendRedirect("/root/sizes/");
    		}
    		
    		$form->includeFeedback($request);
    		$request->addAttribute(self::ATT_SIZE, $size);
    	}
    	
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateSize (Request $request, Response $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	$id = intval($request->getDataGET('id'), 10);
    	
    	if (!$this->sizeDAOManager->checkById($id)) {
    		$response->sendError();
    	}
    	
    	$size = $this->sizeDAOManager->findById($id);
    	
    	if ($request->getMethod() == Request::HTTP_POST) {
    		$form = new SizeFormValidator($this->getDaoManager());
    		$request->addAttribute($form::CHAMP_ID, $id);
    		$size = $form->updateAfterValidation($request);
    		
    		if (!$form->hasError()) {
    			$response->sendRedirect("/root/sizes/");
    		}
    		
    		$form->includeFeedback($request);
    	}
    	
    	$request->addAttribute(self::ATT_SIZE, $size);
    	
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeUpdateOffice (Request $request, Response $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        
        if ($this->officeDAOManager->checkById($id)) {
            $office = $this->officeDAOManager->findById($id);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeFormValidator($this->getDaoManager());
            
            $office = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/offices/');
            }
            
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_OFFICE, $office);
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
    }
    
    
    /***
     * lors du changement de la logique d'impementation du virtual, un refactoring des donnees 
     * nous a ete obligatoire. c'est ainsi que cette action ne respectepas les regles de jeux
     * @param Request $request
     * @param Response $response
     * @deprecated il est structement interdit d'executer cette action.
     */
    public function executeRefactoringVirtual (Request $request, Response $response) : void {
        
        $response->sendError("Reserved to Ing Esaie MUHASA only");
        
        /**
         * @var VirtualMoneyDAOManager $virtualDao
         * @var GradeMemberDAOManager $gradeMemberDao
         */
        $virtualDao = $this->getDaoManager()->getManagerOf(VirtualMoney::class);
        $gradeMemberDao = $this->getDaoManager()->getManagerOf(GradeMember::class);
        $moneyDao = $this->getDaoManager()->getManagerOf(MoneyGradeMember::class);
        
        try {
            $pdo = $this->getDaoManager()->getConnection();
            $pdo->beginTransaction();
            
            /**
             * @var Office[] $offices
             * @var Office $office
             */
            $offices = $this->officeDAOManager->findAll();
            foreach ($offices as $office) {
                if ($office->isCentral() || !$virtualDao->checkByOffice($office->getId())) {
                    continue;//office centrale, ou office sans virtuel
                }
                
                /**
                 * pour faciliter la tache:
                 * - on recupere les virtuels de deja envoyer a l'office
                 * - on on reorganise les champs en ignorant la ruprique d'affiliation
                 * - on genere les occurences de MoneyGradeMemebr
                 * @var VirtualMoney[] $virtuals
                 * @var VirtualMoney $virtual
                 */
                $virtuals = $virtualDao->findByOffice($office->getId());
                $now = new \DateTime();
                    
                foreach ($virtuals as $virtual) {
                    /*
                     * pour chaque virtuel, on verifie s'il y avait des operation de retro-commussion
                     * car les retro commussion doiventetre affecter dans le compte afiliate
                     */
                    $afiliate = ($virtual->getAmount() != $virtual->getExpected() && $virtual->getExpected() > 0)? ($virtual->getExpected() - $virtual->getAmount()) : 0;
                    $virtual->setDateModif($now);
                    
                    $virtual->setProduct($virtual->getAmount());
                    $virtual->setAfiliate($afiliate);
                    UtilitaireSQL::update($pdo, "VirtualMoney", [
                        'product' => $virtual->getAmount(),
                        'afiliate' => $afiliate,
                        'dateModif' => $virtual->getFormatedDateModif(\DateTime::RFC3339)
                    ], $virtual->getId());
                    
                    $virtual->hydrate([
                        'availableProduct' => $virtual->getProduct(),
                        'availableAfiliate' => $afiliate
                    ]);
                    $virtual->setOffice(null);
                }
                
                if (!$gradeMemberDao->checkByOffice($office->getId())) {//office sans affiliation
                    continue;
                }
                
                /**
                 * @var GradeMember[] $members
                 * @var GradeMember $member
                 */
                $members = $gradeMemberDao->findByOffice($office->getId());
                
                /*
                 * parcour de membre affilier dans l'office
                 */
                foreach ($members as $gm) {
                    $moneyGrades = [];//les virtuels toucher pour l'operation
                    
                    $resteProduct = $gm->getProduct();
                    $resteAfiliate = $gm->getMembership();
                    
                    for ($i = 0; $i < count($virtuals); $i++) {
                        $virtual = $virtuals[$i];
                        
                        $product = $virtual->getSubstractableToAvailableProduct($resteProduct);
                        $afiliate = $virtual->getSubstractableToAvailableAfiliate($resteAfiliate);
                        
                        $resteAfiliate -= $afiliate;
                        $resteProduct -= $product;
                        
                        if ($product != 0 || $afiliate != 0) {
                            $money = new MoneyGradeMember();
                            $money->setProduct($product);
                            $money->setAfiliate($afiliate);
                            $money->setVirtualMoney($virtual);
                            $money->setGradeMember($gm);
                            $money->setDateAjout($now);
                            
                            $moneyGrades[] = $money;
                            $virtual->substract($product, $afiliate);
                        }
                    }
                    
                    if (empty($moneyGrades)) {
                        $money = new MoneyGradeMember();
                        $money->setProduct(0);
                        $money->setAfiliate(0);
                        $money->setVirtualMoney($virtuals[0]);
                        $money->setGradeMember($gm);
                        $money->setDateAjout($now);
                        
                        $moneyGrades[] = $money;
                    }
                    
                    /*
                     * dans on verifie le compte afiliation
                     * s'il n'y a pas de compte capable de satisfaire la requette
                     * alors s'il le compte d'afiliation n'est pas satisfaisant, on viole le premier viruel en metant enjour son champs afiliate
                     * si le compte produit ne satisfont pas nos plus, on viole les regles
                     */
                    if ($gm->getOld() == null && $resteAfiliate != 0) {//control du depordement des comptes afiliation
                        $money = $moneyGrades[0];
                        $virtual = $money->getVirtualMoney();
                        $virtual->addOnAfiliate($resteAfiliate);
                        $afiliate = $virtual->getSubstractableToAvailableAfiliate($resteAfiliate);
                        
                        UtilitaireSQL::update($pdo, "VirtualMoney", [
                            'product' => $virtual->getProduct(),
                            'afiliate' => $virtual->getAfiliate(),
                            'dateModif' => $virtual->getFormatedDateModif(\DateTime::W3C)
                        ], $virtual->getId());
                        
                        $virtual->substract(0, $resteAfiliate);
                        $resteAfiliate -= $afiliate;
                        
                        $money->setAfiliate($money->getAfiliate() + $afiliate);
                    }
                    
                    if ($resteProduct  > 0) {//control du debordement des comptes produits
                        $money = $moneyGrades[0];
                        $virtual = $money->getVirtualMoney();
                        $virtual->addOnProduct($resteProduct);
                        $product = $virtual->getSubstractableToAvailableProduct($resteProduct);
                        
                        UtilitaireSQL::update($pdo, "VirtualMoney", [
                            'product' => $virtual->getProduct(),
                            'afiliate' => $virtual->getAfiliate(),
                            'dateModif' => $virtual->getFormatedDateModif(\DateTime::W3C)
                        ], $virtual->getId());
                        
                        $virtual->substract($resteProduct, 0);
                        $resteProduct -= $product;
                        
                        $money->setProduct($money->getProduct() + $product);
                    }
                    
                    $moneyDao->createAllInTransaction($moneyGrades, $pdo);
                }
            }

            $pdo->commit();
        } catch (\Exception $e) {
            $response->sendError($e);
        }
        
        $response->sendRedirect("/root/");
    }
        
}

