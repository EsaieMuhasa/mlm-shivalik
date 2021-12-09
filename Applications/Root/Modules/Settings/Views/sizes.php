<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-tags"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="add.html"><span class="fa fa-plus"></span>News size</a>
    	</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
        <section class="thumbnail">
        	<?php if (!empty($_REQUEST[SettingsController::ATT_SIZES])) { ?>
        	<table class="table table-bordered">
        		<caption class="hidden">Genegations registereds</caption>
        		<thead>
        			<tr>
        				<th>N°</th>
        				<th>Name</th>
        				<th>Abbreviation</th>
        				<th>Percentage</th>
        				<th class="success">Options</th>
        			</tr>
        		</thead>
        		<tbody>
            		
            		<?php  foreach ($_REQUEST[SettingsController::ATT_SIZES] as $key => $size) : ?>
            		<tr>
            			<td><?php echo ($key+1); ?></td>
            			<td><?php echo htmlspecialchars($size->name); ?></td>
            			<td><?php echo htmlspecialchars($size->abbreviation); ?></td>
            			<td><?php echo htmlspecialchars($size->percentage); ?> % </td>
            			
            			<td class="success">
            				<a class="btn btn-xs btn-primary" href="/root/sizes/<?php echo  $size->id; ?>/update.html">Update</a>
            			</td>
            		</tr>
            		<?php endforeach;?>
            		
        		</tbody>
        	</table>
        	<?php } else {?>
        	<div class="alert alert-danger">
        		<p>No offices size in database configuration</p>
        	</div>
        	<?php }?>
        </section>
    </div>
</div>