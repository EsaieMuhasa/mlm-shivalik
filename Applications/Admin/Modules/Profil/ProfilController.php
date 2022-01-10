<?php
namespace Applications\Admin\Modules\Profil;

use Applications\Admin\AdminApplication;
use Core\Shivalik\Managers\CountryDAOManager;
use PHPBackend\Http\HTTPController;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Validators\OfficeAdminFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;

/**
 *
 * @author Esaie MHS
 *        
 */
class ProfilController extends HTTPController
{
    
    const ATT_LOCALISATION = 'localisation';
    const ATT_COUNTRYS = 'countrys';
    
    /**
     * @var CountryDAOManager
     */
    private $countryDAOManager;
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeIndex (Request $request, Response $response) : void {
        
    }
    
    
    /**
     * update password of member
     * @param Request $request
     * @param Response $response
     */
    public function executePassword (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, AdminApplication::getConnectedUser()->getId());
            $form->updatePasswordAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/logout.html");
            }
            
            $form->includeFeedback($request);
        }
    }
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executePhoto (Request $request, Response $response) : void{
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, AdminApplication::getConnectedUser()->getId());
            $user = $form->updatePhotoAfterValidation($request);
            
            if (!$form->hasError()) {
            	AdminApplication::getConnectedUser()->setPhoto($user->getPhoto());
                $response->sendRedirect("/admin/profil/");
            }
            
            $form->includeFeedback($request);
        }
    }
    
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeAddress (Request $request, Response $response) : void{
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new LocalisationFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, AdminApplication::getConnectedUser()->getLocalisation()->getId());
            $localisation = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                AdminApplication::getConnectedUser()->setLocalisation($localisation);
                $response->sendRedirect("/admin/profil/");
            }
            
            $form->includeFeedback($request);
        } else {
            $localisation = AdminApplication::getConnectedUser()->getLocalisation();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
        $request->addAttribute(self::ATT_LOCALISATION, $localisation);
    }

}

