<?php
use Applications\Admin\Modules\Members\MembersController;

/**
 * @var \Entities\Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var \Entities\Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];


?>

<?php require_once '_nav-member.php';?> 
