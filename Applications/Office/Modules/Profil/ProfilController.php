<?php
namespace Applications\Office\Modules\Profil;

use Library\Controller;
use Library\HTTPRequest;
use Library\HTTPResponse;
use Validators\LocalisationFormValidator;
use Managers\CountryDAOManager;
use Validators\OfficeAdminFormValidator;
use Applications\Office\OfficeApplication;

/**
 *
 * @author Esaie MHS
 *        
 */
class ProfilController extends Controller
{
    
    const ATT_LOCALISATION = 'localisation';
    const ATT_COUNTRYS = 'countrys';
    
    /**
     * @var CountryDAOManager
     */
    private $countryDAOManager;
    
    /**
     * {@inheritDoc}
     * @see \Library\Controller::__construct()
     */
    public function __construct(\Library\Application $application, $action, $module)
    {
        parent::__construct($application, $action, $module);
    }
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeIndex (HTTPRequest $request, HTTPResponse $response) : void {
        
    }
    
    
    /**
     * update password of member
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executePassword (HTTPRequest $request, HTTPResponse $response) : void {
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, OfficeApplication::getConnectedUser()->getId());
            $form->updatePasswordAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/logout.html");
            }
            
            $form->includeFeedback($request);
        }
    }
    
    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executePhoto (HTTPRequest $request, HTTPResponse $response) : void{
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, OfficeApplication::getConnectedUser()->getId());
            $user = $form->updatePhotoAfterValidation($request);
            
            if (!$form->hasError()) {
            	OfficeApplication::getConnectedUser()->setPhoto($user->getPhoto());
                $response->sendRedirect("/office/profil/");
            }
            
            $form->includeFeedback($request);
        }
    }
    
    
    /**
     * 
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeAddress (HTTPRequest $request, HTTPResponse $response) : void{
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
            $form = new LocalisationFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, OfficeApplication::getConnectedUser()->getLocalisation()->getId());
            $localisation = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                OfficeApplication::getConnectedUser()->setLocalisation($localisation);
                $response->sendRedirect("/office/profil/");
            }
            
            $form->includeFeedback($request);
        } else {
            $localisation = OfficeApplication::getConnectedUser()->getLocalisation();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
        $request->addAttribute(self::ATT_LOCALISATION, $localisation);
    }

}

