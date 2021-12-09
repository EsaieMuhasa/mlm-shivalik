<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-tags"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li><i class="fa fa-tags"></i><a href="/root/sizes/">Offices size</a></li>
    		<li><i class="fa fa-plus"></i>new size</li>
    	</ol>
    </div>
</div>
<?php require_once '_form-size.php'; ?>