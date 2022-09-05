<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-building"></i><?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="/root/offices/add.html"><span class="fa fa-plus"></span>New office</a>
    	</div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
    	<?php if (!empty($_REQUEST[SettingsController::ATT_OFFICES])) { ?>
    	<div class="row">
    		<?php  foreach ($_REQUEST[SettingsController::ATT_OFFICES] as $office) : ?>
    		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        		<a class="thumbnail" href="/root/offices/<?php echo  $office->id; ?>/update.html">
        			<strong style="display: block;font-size: 2rem;"><?php echo htmlspecialchars($office->name); ?></strong>
        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($office->name); ?>" src="/<?php echo ($office->photo); ?>">
        			<em class="badge"><?php echo ($office->central? 'Centrale':'auxiliaire'); ?></em>
        		</a>
             </div>
    		<?php endforeach;?>
    	</div>
    	<?php } else {?>
    	<div class="alert alert-danger">
    		<p>No grade in database configuration</p>
    	</div>
    	<?php } ?>
    </div>
</div>