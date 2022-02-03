<?php
namespace Applications\Common\Modules\Authentification;

use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Validators\MemberFormValidator;
use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Filters\SessionMemberFilter;

/**
 *
 * @author Esaie MHS
 *        
 */
class AuthentificationController extends HTTPController
{
    const ATT_USER = 'user';
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /***
     * Connection process
     * if user is connected, connction form has ben inaccessible
     * @param Request $request
     * @param Response $response
     */
    public function executeLogin (Request $request, Response $response) : void {
        $this->redirect($request, $response);
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new MemberFormValidator($this->getDaoManager());
            $user = $form->connectionProcess($request);
            
            if (!$form->hasError()) {
                if ($user  instanceof OfficeAdmin) {
                    if ($user->getOffice()->isCentral()) {
                        $request->getSession()->addAttribute(SessionAdminFilter::ADMIN_CONNECTED_SESSION, $user);
                    }else {
                    	$request->getSession()->addAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION, $user);
                    }
                }else if ($user  instanceof Member) {
                    if ($this->officeDAOManager->checkByMember($user->getId())) {//our les utilisateur qui ont des bureau
                        $office = $this->officeDAOManager->findByMember($user->getId());
                        $user->setOfficeAccount($office);
                    }
                    
                    $request->getSession()->addAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION, $user);
                }else {
                    $response->sendError();
                }
                
                $this->redirect($request, $response);
            }
            
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_USER, $user);
        }
    }
    
    /**
     * Depamde de redirection de l'utilisateur actuelement connecteer
     * @param Request $request
     * @param Response $response
     */
    private function redirect (Request $request, Response $response) : void {
        if ($request->getSession()->hasAttribute(SessionAdminFilter::ADMIN_CONNECTED_SESSION)) {
            $response->sendRedirect("/admin/");
        }
        
        if ($request->getSession()->hasAttribute(SessionMemberFilter::MEMBER_CONNECTED_SESSION)) {
            $response->sendRedirect("/member/");
        }
        
        if ($request->getSession()->hasAttribute(SessionOfficeFilter::OFFICE_CONNECTED_SESSION)) {
            $response->sendRedirect("/office/");
        }
    }
    
    /**
     * demande d'adhesion effectuer par un internaute
     * @param Request $request
     * @param Response $response
     */
    public function executeInscription (Request $request, Response $response) : void {
        $this->redirect($request, $response);
    }
    
    /**
     * 
     * @param Request $request
     * @param Response $response
     */
    public function executeLogout (Request $request, Response $response) : void {
        session_destroy();
        $response->sendRedirect("/");
    }
}

