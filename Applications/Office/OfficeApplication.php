<?php

namespace Applications\Office;


use PHPBackend\Http\HTTPApplication;
use Core\Shivalik\Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeApplication extends HTTPApplication {

	const ATT_CONNETED_OFFICE_ADMIN = "CONNECTED_OFFICE_ADMIN_";


	/**
	 * {@inheritDoc}
	 * @see HTTPApplication::run()
	 */
	public function run() : void {
		if (self::getConnectedUser() != null) {
			parent::run();
		}else {
			$this->getHttpRequest()->forward('login', 'Authentification', 'Index');
		}
	}

	/**
	 * @return OfficeAdmin|NULL
	 */
	public static final function getConnectedUser () : ?OfficeAdmin {
		if (isset($_SESSION[self::ATT_CONNETED_OFFICE_ADMIN]) && $_SESSION[self::ATT_CONNETED_OFFICE_ADMIN] instanceof OfficeAdmin && !$_SESSION[self::ATT_CONNETED_OFFICE_ADMIN]->office->isCentral()) {
			return $_SESSION[self::ATT_CONNETED_OFFICE_ADMIN];
		}
		return null;
	}


}

