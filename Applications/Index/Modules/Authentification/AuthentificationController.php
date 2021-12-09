<?php
namespace Applications\Index\Modules\Authentification;

use Library\Controller;
use Library\HTTPRequest;
use Library\HTTPResponse;
use Validators\MemberFormValidator;
use Entities\OfficeAdmin;
use Entities\Member;
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
use Managers\OfficeDAOManager;

/**
 *
 * @author Esaie MHS
 *        
 */
class AuthentificationController extends Controller
{
    const ATT_USER = 'user';
    
    /**
     * @var OfficeDAOManager
     */
    private $officeDAOManager;
    
    /***
     * Connection process
     * if user is connected, connction form has ben inaccessible
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeLogin (HTTPRequest $request, HTTPResponse $response) : void {
        
        if (AdminApplication::getConnectedUser()!=null) {
            $response->sendRedirect("/admin/");
        }
        
        if (MemberApplication::getConnectedMember()!=null) {
            $response->sendRedirect("/member/");
        }
        
        if (OfficeApplication::getConnectedUser()!=null) {
        	$response->sendRedirect("/office/");
        }
        
        
        if ($request->getMethod() == HTTPRequest::HTTP_POST) {
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
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function executeLogout (HTTPRequest $request, HTTPResponse $response) : void {
        session_destroy();
        $response->sendRedirect("/");
    }
}

