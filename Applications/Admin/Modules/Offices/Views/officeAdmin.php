<?php
use Applications\Admin\Modules\Offices\OfficesController;
?>



<?php if (!isset($_REQUEST[OfficesController::ATT_OFFICE_ADMIN]) || $_REQUEST[OfficesController::ATT_OFFICE_ADMIN] == null) { ?>
<a class="btn btn-primary" href="admin/new.html">
	<span class="fa fa-plus"></span> Create an administrator account for this office
</a>
<?php } else { 
$admin = $_REQUEST[OfficesController::ATT_OFFICE_ADMIN]; ?>
<div class="row">
	<div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-6  col-sm-offset-3 col-xs-10  col-xs-offset-1">
		<div class="panel panel-default">
        	<div class="panel-heading">
        		<strong style="font-size: 1.6rem;" class="panel-title"><?php echo htmlspecialchars("{$admin->names}"); ?></strong>      				
        	</div>
        	<div class="panel-body">
		        <span class="thumbnail">
	        		<img class="image-rond" alt="icon <?php echo htmlspecialchars($admin->names); ?>" src="/<?php echo ($admin->photo); ?>">
		        </span>
		        <span class="label label-info"><?php echo htmlspecialchars($admin->email); ?></span>
        	</div>
        	<div class="panel-footer">
        		<a class="btn btn-<?php echo ($admin->enable? 'danger':'primary'); ?>" href="<?php echo  ($admin->enable? "disable":"enable")."-".($admin->id); ?>.html">
	        		<span class="glyphicon glyphicon-<?php echo ($admin->enable? 'remove':'ok'); ?>"></span>
	        		<?php echo ($admin->enable? 'Disable':'Enable'); ?> account
		        </a>
		        <a class="btn btn-success" href="<?php echo  ("reset-{$admin->id}"); ?>.html">Reset password</a>
        	</div>
        </div>
	</div>
</div>
<?php } ?>