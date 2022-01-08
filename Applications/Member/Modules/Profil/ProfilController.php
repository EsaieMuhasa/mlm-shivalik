<?php
namespace Applications\Member\Modules\Profil;

use Applications\Member\MemberApplication;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\CountryDAOManager;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Validators\LocalisationFormValidator;
use Core\Shivalik\Validators\MemberFormValidator;

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
            $form = new MemberFormValidator($this->getDaoManager());
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
            $form = new MemberFormValidator($this->getDaoManager());
            $form->updatePhotoAfterValidation($request);
            
            if (!$form->hasError()) {
                $response->sendRedirect("/member/profil/");
            }
            
            $form->includeFeedback($request);
        }
    }
    
    
    /**
     * @param Request $request
     * @param Response $response
     */
    public function executeAddress (Request $request, Response $response) : void{
        
        if ($request->getMethod() == Request::HTTP_POST) {
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

