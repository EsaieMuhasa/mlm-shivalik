<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];


?>

<?php require_once '_nav-member.php';?> 
