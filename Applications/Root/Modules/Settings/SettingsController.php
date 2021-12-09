<?php
namespace Applications\Root\Modules\Settings;

use Library\Controller;
use Library\HTTPRequest;
use Library\HTTPResponse;
use Applications\Root\RootApplication;
use Validators\OfficeAdminFormValidator;
use Managers\OfficeAdminDAOManager;
use Managers\OfficeDAOManager;
use Managers\GenerationDAOManager;
use Managers\GradeDAOManager;
use Managers\CountryDAOManager;
use Validators\GradeFormValidator;
use Validators\GenerationFormValidator;
use Validators\OfficeFormValidator;
use Validators\CountryFormValidator;
use Validators\LocalisationFormValidator;
use Validators\SizeFormValidator;
use Managers\SizeDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class SettingsController extends Controller
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
	 * {@inheritDoc}
	 * @see \Library\Controller::__construct()
	 */
	public function __construct(\Library\Application $application, $action, $module) {
		parent::__construct($application, $action, $module);
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Settings");
	}

	/**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeLogin (HTTPRequest $request, HTTPResponse $response) : void {
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST){
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
                $_SESSION[RootApplication::ATT_CONNECTED_ROOT] = $root;
                $response->sendRedirect($request->getURI());
            }else {
                $request->addAttribute('user', $root);
                $request->addAttribute('errors', $errors);
                $request->addAttribute('result', 'Connection failure');
            }
        }
    }

    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeIndex (HTTPRequest $request, HTTPResponse $response) : void {
        
    }
    
    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeServer (HTTPRequest $request, HTTPResponse $response) : void {
    	
    }
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAdmins (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        if ($this->officeAdminDAOManager->countAll() > 0) {
            $request->addAttribute(self::ATT_ADMINS, $this->officeAdminDAOManager->getAll());
        }else {
            $request->addAttribute(self::ATT_ADMINS, array());
        }
    }
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddAdmin (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
        $request->addAttribute(self::ATT_OFFICES, $this->officeDAOManager->getAll());
    }
    
    
    /**
     * to update the admin acount
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateAdmin (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "System admin");
        
        $id = intval($request->getDataGET('id'), 10);
        if ($this->officeAdminDAOManager->idExist($id)) {
            $admin = $this->officeAdminDAOManager->getForId($id);
        } else {
            $response->sendError();
        }
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $admin = $form->updateAfterValidation($request);
            if (!$form->hasError()) {
                $response->sendRedirect('/root/admins/');
            }
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_ADMIN, $admin);
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
    }
    
    
    /***
     * process action to show all grade in database
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeGrades (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        if ($this->gradeDAOManager->countAll() > 0) {
            $request->addAttribute(self::ATT_GRADES, $this->gradeDAOManager->getAll());
        }else {
            $request->addAttribute(self::ATT_GRADES, array());
        }
        
    }
    
    
    /**
     * process action to create a new grade
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddGrade (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        if ($this->generationDAOManager->countAll()>=0) {
            $generations = $this->generationDAOManager->getAll();
        }else {
            $generations = array();
        }
        
        $request->addAttribute(self::ATT_GENERATIONS, $generations);
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateGrade (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Grades configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        
        if ($this->gradeDAOManager->idExist($id)) {
            $grade = $this->gradeDAOManager->getForId($id);
            if ($this->generationDAOManager->countAll()>=0) {
                $generations = $this->generationDAOManager->getAll();
            }else {
                $generations = array();
            }
            $request->addAttribute(self::ATT_GENERATIONS, $generations);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeGenerations (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        if ($this->generationDAOManager->countAll() > 0){
            $generations = $this->generationDAOManager->getAll();
        }else {
            $generations = array();
        }
        
        $request->addAttribute(self::ATT_GENERATIONS, $generations);
    }
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeCountrys (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        if ($this->countryDAOManager->countAll() > 0) {
            $countrys = $this->countryDAOManager->getAll();
        }else {
            $countrys = array();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $countrys);
    }
    
    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddCountry (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateCountry (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Countrys configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        if ($this->countryDAOManager->idExist($id)) {
            $country = $this->countryDAOManager->getForId($id);
        } else {
            $response->sendError();
        }
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddGeneration (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateGeneration (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Generations configuration");
        
        $id = intval($request->getDataGET('id'));
        
        if ($this->generationDAOManager->idExist($id)) {
            $generation = $this->generationDAOManager->getForId($id);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeOffices (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        if ($this->officeDAOManager->countAll() > 0){
            $offices = $this->officeDAOManager->getAll();
        }else {
            $offices = array();
        }
        
        $request->addAttribute(self::ATT_OFFICES, $offices);
    }
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddOffice (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
    }
    
    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeSizes(HTTPRequest $request, HTTPResponse $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	
    	if ($this->sizeDAOManager->countAll() <= 0){
    		$sizes = array();
    	}else {
    		$sizes = $this->sizeDAOManager->getAll();
    	}
    	
    	$request->addAttribute(self::ATT_SIZES, $sizes);
    }
    
    
    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddSize(HTTPRequest $request, HTTPResponse $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateSize (HTTPRequest $request, HTTPResponse $response) : void {
    	$request->addAttribute(self::ATT_VIEW_TITLE, "Offices size configuration");
    	$id = intval($request->getDataGET('id'), 10);
    	
    	if (!$this->sizeDAOManager->idExist($id)) {
    		$response->sendError();
    	}
    	
    	$size = $this->sizeDAOManager->getForId($id);
    	
    	if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeUpdateOffice (HTTPRequest $request, HTTPResponse $response) : void {
        $request->addAttribute(self::ATT_VIEW_TITLE, "Offices configuration");
        
        $id = intval($request->getDataGET('id'), 10);
        
        if ($this->officeDAOManager->idExist($id)) {
            $office = $this->officeDAOManager->getForId($id);
        }else {
            $response->sendError();
        }
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            $form = new OfficeFormValidator($this->getDaoManager());
            
            $office = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect('/root/offices/');
            }
            
            $form->includeFeedback($request);
        }
        
        $request->addAttribute(self::ATT_OFFICE, $office);
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
    }
    
    
}

