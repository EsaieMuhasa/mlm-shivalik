<?php
namespace Applications\Admin;

use Library\Application;
use Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
class AdminApplication extends Application
{
    const CONNECTED_USER = 'CONNECTED_USER';
    
    /**
     * {@inheritDoc}
     * @see \Library\Application::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Admin';
    }

    /**
     * {@inheritDoc}
     * @see \Library\Application::run()
     */
    public function run()
    {
        if (self::getConnectedUser() != null) {
            return parent::run();
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

