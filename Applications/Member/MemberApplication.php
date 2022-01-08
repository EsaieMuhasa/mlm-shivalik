<?php
namespace Applications\Member;

use PHPBackend\Http\HTTPApplication;
use Core\Shivalik\Entities\Member;

/**
 *
 * @author Esaie MHS
 *        
 */
class MemberApplication extends HTTPApplication
{
    const ATT_CONNECTED_MEMBER = 'SESSION_MEMBER_CONNECTED_USER';

    /**
     * {@inheritDoc}
     * @see HTTPApplication::run()
     */
    public function run() : void
    {
        if (self::getConnectedMember() != null) {
            if (self::getConnectedMember()->isEnable()) {
                parent::run();
            }else {
                $this->getRequest()->forward('disabled', 'Account');
            }
        }else {
            $this->getRequest()->forward('login', 'Authentification', 'Index');
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

