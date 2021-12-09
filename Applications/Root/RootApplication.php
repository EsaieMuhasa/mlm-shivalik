<?php
namespace Applications\Root;

use Library\Application;
use Library\User;

/**
 *
 * @author Esaie MHS
 *        
 */
class RootApplication extends Application
{
    const ATT_CONNECTED_ROOT = 'CONNETED_USER_ROOT';
    
    /**
     * {@inheritDoc}
     * @see \Library\Application::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Root';
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Library\Application::run()
     */
    public function run()
    {
        if (isset($_SESSION[self::ATT_CONNECTED_ROOT])  && $_SESSION[self::ATT_CONNECTED_ROOT] instanceof User) {            
            parent::run();
        }else {
            $this->getHttpRequest()->forward('login', 'Settings');
        }
    }

}

