<?php
namespace Core\Shivalik\Filters;

use PHPBackend\Http\HTTPFilter;
use PHPBackend\Request;
use PHPBackend\Response;
use Core\Shivalik\Entities\OfficeAdmin;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class SessionOfficeFilter extends HTTPFilter
{
    const OFFICE_CONNECTED_SESSION = 'Office_Admin_Connected_in_session';

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Filter::doFilter()
     */
    public function doFilter(Request $request, Response $response) : void
    {
        if ($request->getSession()->hasAttribute(self::OFFICE_CONNECTED_SESSION) && $request->getSession()->getAttribute(self::OFFICE_CONNECTED_SESSION) instanceof OfficeAdmin) {
            /**
             * @var OfficeAdmin $admin
             */
            $admin = $request->getSession()->getAttribute(self::OFFICE_CONNECTED_SESSION);
            
            if ($admin->isEnable()) {
                return;
            }
        }
        
        $request->forward("login", "Authentification", "Common");
    }
}

