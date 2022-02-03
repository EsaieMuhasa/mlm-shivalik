<?php
namespace Core\Shivalik\Filters;

use PHPBackend\Http\HTTPFilter;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Entities\OfficeAdmin;

class SessionAdminFilter extends HTTPFilter
{
    const ADMIN_CONNECTED_SESSION = 'Shivalik_Admin_in_Session';
    
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Filter::doFilter()
     */
    public function doFilter(Request $request, Response $response): void
    {
        if ($request->getSession()->hasAttribute(self::ADMIN_CONNECTED_SESSION) && ($request->getSession()->getAttribute(self::ADMIN_CONNECTED_SESSION) instanceof OfficeAdmin)) {
            return;
        }
        
        $request->forward("login", "Authentification", "Common");
    }


}

