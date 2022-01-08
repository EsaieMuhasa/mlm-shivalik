<?php
namespace Applications\Root;
use PHPBackend\Http\HTTPApplication;
use PHPBackend\User;

/**
 *
 * @author Esaie MHS
 *        
 */
class RootApplication extends HTTPApplication
{
    const ATT_CONNECTED_ROOT = 'CONNETED_USER_ROOT';
    
    /**
     * {@inheritDoc}
     * @see HTTPApplication::run()
     */
    public function run()
    {
        if (isset($_SESSION[self::ATT_CONNECTED_ROOT])  && $_SESSION[self::ATT_CONNECTED_ROOT] instanceof User) {            
            parent::run();
        }else {
            $this->getRequest()->forward('login', 'Settings');
        }
    }

}

