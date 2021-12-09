<?php
use Applications\Root\Modules\Settings\SettingsController;
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-money"></i><?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<span class="fa fa-money"></span>
    			<a href="/member/withdrawals/"> Withdrawals</a>
			</li>
			<li><i class="fa fa-tag"></i><?php echo $_GET['id']; ?></li>
    		<li><i class="fa fa-edit"></i>Update</li>
    	</ol>
    </div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php require_once '_form-withdrawal.php';?>
	</div>
</div>