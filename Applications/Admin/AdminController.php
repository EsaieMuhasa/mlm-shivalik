<?php

namespace Applications\Admin;


use Core\Shivalik\Managers\BonusGenerationDAOManager;
use Core\Shivalik\Managers\MemberDAOManager;
use Core\Shivalik\Managers\PointValueDAOManager;
use Core\Shivalik\Managers\WithdrawalDAOManager;
use PHPBackend\Application;
use PHPBackend\Http\HTTPController;

/**
 *
 * @author Esaie MHS
 *        
 */
abstract class AdminController extends HTTPController {
	
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
	 * {@inheritdoc}
	 * @see \PHPBackend\Http\HTTPController::__construct()
	 */
	public function __construct(Application $application, string $action, string $module) {
		parent::__construct($application, $action, $module);
		$application->getHttpRequest()->addAttribute(self::ATT_VIEW_TITLE, "Shivalik");
	}

}

