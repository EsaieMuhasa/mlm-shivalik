<?php
namespace Applications\Admin\Modules\Profil;

use Applications\Admin\AdminController;
use Core\Shivalik\Managers\CountryDAOManager;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Validators\OfficeAdminFormValidator;
use Core\Shivalik\Validators\LocalisationFormValidator;
use Core\Shivalik\Filters\SessionAdminFilter;

/**
 *
 * @author Esaie MHS
 *        
 */
class ProfilController extends AdminController
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
        $request->addAttribute("user", $request->getSession()->getAttribute(SessionAdminFilter::ADMIN_CONNECTED_SESSION));
    }
    
    
    /**
     * update password of member
     * @param Request $request
     * @param Response $response
     */
    public function executePassword (Request $request, Response $response) : void {
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new OfficeAdminFormValidator($this->getDaoManager());
            $request->addAttribute($form::CHAMP_ID, $request->getSession()->getAttribute(SessionAdminFilter::ADMIN_CONNECTED_SESSION)->getId());
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
            $request->addAttribute($form::CHAMP_ID, $this->getConnectedAdmin()->getId());
            $user = $form->updatePhotoAfterValidation($request);
            
            if (!$form->hasError()) {
            	$this->getConnectedAdmin()->setPhoto($user->getPhoto());
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
            $request->addAttribute($form::CHAMP_ID, $this->getConnectedAdmin()->getLocalisation()->getId());
            $localisation = $form->updateAfterValidation($request);
            
            if (!$form->hasError()) {
                $this->getConnectedAdmin()->setLocalisation($localisation);
                $response->sendRedirect("/admin/profil/");
            }
            
            $form->includeFeedback($request);
        } else {
            $localisation = $this->getConnectedAdmin()->getLocalisation();
        }
        
        $request->addAttribute(self::ATT_COUNTRYS, $this->countryDAOManager->findAll());
        $request->addAttribute(self::ATT_LOCALISATION, $localisation);
    }

}

