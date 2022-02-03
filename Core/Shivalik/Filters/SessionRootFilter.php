<?php
namespace Core\Shivalik\Filters;

use PHPBackend\Http\HTTPFilter;
use PHPBackend\Request;
use PHPBackend\Response;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class SessionRootFilter extends HTTPFilter
{
    const ROOT_CONNECTED_SESSION = 'Root_Data_inSession';

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Filter::doFilter()
     */
    public function doFilter(Request $request, Response $response) : void
    {
        if(!$request->getSession()->hasAttribute(self::ROOT_CONNECTED_SESSION)){
            $request->forward("login", "Settings");
        }
    }
}

