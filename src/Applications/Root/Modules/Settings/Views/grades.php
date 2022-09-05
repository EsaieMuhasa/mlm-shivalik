<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-graduation-cap"></i> Grades configuration</h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="/root/grades/add.html"><span class="fa fa-plus"></span>News grade</a>
    	</div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
    	<?php if (!empty($_REQUEST[SettingsController::ATT_GRADES])) { ?>
    	<div class="row">
    		<?php  foreach ($_REQUEST[SettingsController::ATT_GRADES] as $grade) : ?>
    		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="padding-bottom: 30px;">
        		<a class="thumbnail" href="/root/grades/<?php echo  $grade->id; ?>/update.html">
        			<strong style="display: block;font-size: 2rem;"><?php echo htmlspecialchars($grade->name); ?></strong>
        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($grade->name); ?>" src="/<?php echo ($grade->icon); ?>">
        			<em class="badge"><?php echo htmlspecialchars($grade->percentage); ?>% </em>
        			<em class="label label-danger">up to <?php echo htmlspecialchars($grade->maxGeneration->name); ?></em>
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