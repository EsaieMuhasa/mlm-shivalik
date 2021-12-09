<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-graduation-cap"></i><?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li><i class="fa fa-users"></i><a href="/root/grades/">Grades</a></li>
    		<li><i class="fa fa-plus"></i>New grade</li>
    	</ol>
    </div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php require_once '_form-grade.php';?>
	</div>
</div>