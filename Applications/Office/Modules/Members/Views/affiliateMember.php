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
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-users"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-users"></i>
    			<a href="/office/members/">Members</a>
			</li>
			<li>
    			<i class="fa fa-user"></i>
    			<a href="/office/members/<?php echo $member->id; ?>/"><?php echo $member->getNames(); ?></a>
			</li>
    		<li><i class="fa fa-plus"></i>Affiliation</li>
    	</ol>
    </div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php if ($office->getAvailableVirtualMoney() < 2) { ?>
		<div class="alert alert-danger">
			<h2 class="alert-title">Warning</h2>
			<p>impossible to perform this operation because the member wallet is insufficient</p>
		</div>
		<?php } else { ?>
			<?php require_once '_form-member.php';?>
		<?php }?>
	</div>
</div>