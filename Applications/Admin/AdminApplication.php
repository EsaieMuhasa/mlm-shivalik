<?php
namespace Applications\Admin;


use PHPBackend\Http\HTTPApplication;
use Core\Shivalik\Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
class AdminApplication extends HTTPApplication
{
    const CONNECTED_USER = 'CONNECTED_USER';

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Http\HTTPApplication::run()
     */
    public function run() : void
    {
        if (self::getConnectedUser() != null) {
            parent::run();
        }else {
            $this->getHttpRequest()->forward('login', 'Authentification', 'Index');
        }
    }
    
    /**
     * 
     * @return OfficeAdmin|NULL
     */
    public static function getConnectedUser() : ?OfficeAdmin{
        if (isset($_SESSION[AdminApplication::CONNECTED_USER]) && $_SESSION[AdminApplication::CONNECTED_USER] instanceof OfficeAdmin && $_SESSION[AdminApplication::CONNECTED_USER]->office->isCentral()) {
            return $_SESSION[AdminApplication::CONNECTED_USER];
        }
        return null;
    }


    
}

