<?php
namespace Core\Shivalik\Filters;

use PHPBackend\Http\HTTPFilter;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Entities\Member;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class SessionMemberFilter extends HTTPFilter
{
    const MEMBER_CONNECTED_SESSION = 'Member_in_corrent_Session';

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
            
            if ($member->isEnable()) {
                return;
            }
        }
        
        $request->forward("login", "Authentification", "Common");
    }
}

