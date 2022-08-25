<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Applications\Office\Modules\Members\MembersController;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\Member;

/**
 * @var Office $office
 * @var Member $member
 */
$office = $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION]->getOffice();
$member = $_REQUEST[MembersController::ATT_SPONSOR];
?>
<div class="row">
	<div class="col-xs-12">
		<?php if ($office->getAvailableVirualMoneyAfiliate() < 20) { ?>
		<div class="alert alert-danger">
			<h2 class="alert-title">Warning</h2>
			<p>impossible to perform this operation because the member wallet is insufficient</p>
		</div>
		<?php } else { ?>
			<?php require_once '_form-member.php';?>
		<?php }?>
	</div>
</div>