<?php
namespace Applications\Member\Modules\Profil;

use Library\Controller;
use Library\HTTPRequest;
use Library\HTTPResponse;
use Validators\MemberFormValidator;
use Validators\LocalisationFormValidator;
use Applications\Member\MemberApplication;
use Managers\CountryDAOManager;

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
            $form = new MemberFormValidator($this->getDaoManager());
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
            $form = new MemberFormValidator($this->getDaoManager());
            $form->updatePhotoAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/profil/");
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
            $request->addAttribute($form::CHAMP_ID, MemberApplication::getConnectedMember()->getLocalisation()->getId());
            $localisation = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                MemberApplication::getConnectedMember()->setLocalisation($localisation);
                $response->sendRedirect("/member/profil/");
            }
            
            $form->includeFeedback($request);
        } else {
            $localisation = MemberApplication::getConnectedMember()->getLocalisation();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->getAll());
        $request->addAttribute(self::ATT_LOCALISATION, $localisation);
    }


}

