<?php
namespace Core\Shivalik\Filters;

use PHPBackend\Http\HTTPFilter;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class SessionMemberFilter extends HTTPFilter
{
    const MEMBER_CONNECTED_SESSION = 'Member_in_corrent_Session';
    const ATT_MONTHLY_ORDER_FOR_ACCOUNT = 'MONTHLY_ORDER_FOR_ACCOUNT';
    
    /**
     * @var MonthlyOrderDAOManager
     */
    private $monthlyOrderDAOManager;

    /**
     * @var MemberDAOManager
     */
    private $memberDAOManager;

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Filter::doFilter()
     */
    public function doFilter(Request $request, Response $response) : void
    {
        if ($request->getSession()->hasAttribute(self::MEMBER_CONNECTED_SESSION) && $request->getSession()->getAttribute(self::MEMBER_CONNECTED_SESSION) instanceof Member) {
            /**
             * @var Member $member
             */
            $member = $request->getSession()->getAttribute(self::MEMBER_CONNECTED_SESSION);

            /** @var Member */
            $member = $this->memberDAOManager->findById($member->getId());
            $request->getSession()->addAttribute(self::MEMBER_CONNECTED_SESSION, $member);
            
            if ($member->isEnable()) {
                
                if($this->monthlyOrderDAOManager->checkByMemberOfMonth($member->getId())){
                    $monthly = $this->monthlyOrderDAOManager->findByMemberOfMonth($member->getId());
                    $request->addAttribute(self::ATT_MONTHLY_ORDER_FOR_ACCOUNT, $monthly);
                }
                return;
            }
        }
        
        $request->forward("login", "Authentification", "Common");
    }
}

