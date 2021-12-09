<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-users"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="/root/admins/add.html"><span class="fa fa-plus"></span>News acount</a>
    	</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
    	<?php if (!empty($_REQUEST[SettingsController::ATT_ADMINS])) { ?>
    	<div class="row">
    		<?php  foreach ($_REQUEST[SettingsController::ATT_ADMINS] as $admin) : ?>
    		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        		<a class="thumbnail" href="/root/grades/<?php echo  $admin->id; ?>/update.html">
        			<strong style="display: block;font-size: 2rem;"><?php echo htmlspecialchars("{$admin->name} {$admin->postName} {$admin->lastName}"); ?></strong>
        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($admin->name); ?>" src="/<?php echo ($admin->photo); ?>">
        			<em class="badge"style="display: block;"><?php echo htmlspecialchars("{$admin->email}"); ?> </em>
        			<em class="label label-danger" style="display: block;"><?php echo htmlspecialchars("{$admin->telephone}"); ?></em>
        		</a>
             </div>
    		<?php endforeach;?>
    	</div>
    	<?php } else {?>
    	<div class="alert alert-danger">
    		<p>No grade in database configuration</p>
    	</div>
    	<?php }?>
    </div>
</div>
