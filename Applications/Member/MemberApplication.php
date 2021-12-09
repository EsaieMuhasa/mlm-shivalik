<?php
namespace Applications\Member;

use Library\Application;
use Entities\Member;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberApplication extends Application
{
    const ATT_CONNECTED_MEMBER = 'SESSION_MEMBER_CONNECTED_USER';
    
    /**
     * {@inheritDoc}
     * @see \Library\Application::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Member';
    }

    /**
     * {@inheritDoc}
     * @see \Library\Application::run()
     */
    public function run()
    {
        if (self::getConnectedMember() != null) {
            if (self::getConnectedMember()->isEnable()) {
                return parent::run();
            }else {
                $this->getHttpRequest()->forward('disabled', 'Account');
            }
        }else {
            $this->getHttpRequest()->forward('login', 'Authentification', 'Index');
        }
    }
    
    /**
     * Revoie le membre actuelement connecter
     * @return Member
     */
    public static function getConnectedMember () : ?Member {
        
        if (isset($_SESSION[self::ATT_CONNECTED_MEMBER])) {
            return $_SESSION[self::ATT_CONNECTED_MEMBER];
        }
        
        return null;
    }


}

