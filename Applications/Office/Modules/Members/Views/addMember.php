<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Entities\Office;

/**
 * @var Office $office
 */
$office = $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION]->getOffice();
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-users"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-users"></i>
    			<a href="/office/members/">Members</a>
			</li>
    		<li><i class="fa fa-plus"></i>New Acount</li>
    	</ol>
    </div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php if ($office->getAvailableVirtualMoneyProduct() < 60 || $office->getAvailableVirualMoneyAfiliate() < 20) { ?>
		<div class="alert alert-danger">
			<h2 class="alert-title">Warning</h2>
			<p>impossible to perform this operation because the office wallet is insufficient</p>
		</div>
		<?php } else { ?>
			<?php require_once '_form-member.php';?>
		<?php }?>
	</div>
</div>