<?php

namespace Applications\Admin;

use Library\Controller;
use Managers\MemberDAOManager;
use Managers\WithdrawalDAOManager;
use Managers\PointValueDAOManager;
use Managers\BonusGenerationDAOManager;
use Entities\Account;
use Entities\Member;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AdminController extends Controller {
	
	const PARAM_MEMBER_COUNT = 'countMembers';
	
	/**
	 * @var MemberDAOManager
	 */
	protected $memberDAOManager;
	
	/**
	 * @var WithdrawalDAOManager
	 */
	protected $withdrawalDAOManager;
	
	/**
	 * @var PointValueDAOManager
	 */
	protected $pointValueDAOManager;
	
	/**
	 * @var BonusGenerationDAOManager
	 */
	protected $bonusGenerationDAOManager;
	
	/**
	 * {@inheritDoc}
	 * @see \Library\Controller::__construct()
	 */
	public function __construct(\Library\Application $application, $action, $module) {
		parent::__construct($application, $action, $module);
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Shivalik");
	}


	/**
	 * @param Member $member
	 * @return Account
	 */
	protected function getAccount (Member $member) : Account {
		return $this->memberDAOManager->getAccount($member);
	}

}

