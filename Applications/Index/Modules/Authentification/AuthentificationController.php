<?php
namespace Applications\Index\Modules\Authentification;

use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
use PHPBackend\Http\HTTPController;
use Core\Shivalik\Managers\OfficeDAOManager;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Validators\MemberFormValidator;
use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Entities\Member;

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
        
        if (AdminApplication::getConnectedUser()!=null) {
            $response->sendRedirect("/admin/");
        }
        
        if (MemberApplication::getConnectedMember()!=null) {
            $response->sendRedirect("/member/");
        }
        
        if (OfficeApplication::getConnectedUser()!=null) {
        	$response->sendRedirect("/office/");
        }
        
        
        if ($request->getMethod() == Request::HTTP_POST) {
            $form = new MemberFormValidator($this->getDaoManager());
            $user = $form->connectionProcess($request);
            
            if (!$form->hasError()) {
                if ($user  instanceof OfficeAdmin) {
                    if ($user->getOffice()->isCentral()) {
                        $_SESSION[AdminApplication::CONNECTED_USER] = $user;
                        $response->sendRedirect("/admin/");
                    }else {
                    	$_SESSION[OfficeApplication::ATT_CONNETED_OFFICE_ADMIN] = $user;
                        $response->sendRedirect("/office/");
                    }
                }else if ($user  instanceof Member) {
                    if ($this->officeDAOManager->hasOffice($user->getId())) {//our les utilisateur qui ont des bureau
                        $office = $this->officeDAOManager->forMember($user->getId());
                        $user->setOfficeAccount($office);
                    }
                    
                    $_SESSION[MemberApplication::ATT_CONNECTED_MEMBER] = $user;
                    $response->sendRedirect("/member/");
                }else {
                    $response->sendError();
                }
            }
            
            $form->includeFeedback($request);
            $request->addAttribute(self::ATT_USER, $user);
        }
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

