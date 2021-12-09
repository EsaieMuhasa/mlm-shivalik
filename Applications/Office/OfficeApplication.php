<?php

namespace Applications\Office;

use Library\Application;
use Entities\OfficeAdmin;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeApplication extends Application {

	const ATT_CONNETED_OFFICE_ADMIN = "CONNECTED_OFFICE_ADMIN_";
	
	/**
	 * {@inheritDoc}
	 * @see \Library\Application::__construct()
	 */
	public function __construct() {
		parent::__construct();
		$this->name = 'Office';
	}

	/**
	 * {@inheritDoc}
	 * @see \Library\Application::run()
	 */
	public function run() {
		if (self::getConnectedUser() != null) {
			return parent::run();
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

