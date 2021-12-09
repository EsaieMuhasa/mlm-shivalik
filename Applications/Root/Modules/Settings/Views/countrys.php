<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-flag"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="/root/countrys/add.html"><span class="fa fa-plus"></span>News country</a>
    	</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
    	<?php if (!empty($_REQUEST[SettingsController::ATT_COUNTRYS])) { ?>
        <section class="thumbnail">
        	<table class="table table-bordered">
        		<caption class="hidden">System countrys</caption>
        		<thead>
        			<tr>
        				<th>N°</th>
        				<th>Name</th>
        				<th>Abbreviation</th>
        				<th class="success">Options</th>
        			</tr>
        		</thead>
        		<tbody>
            		
            		<?php  foreach ($_REQUEST[SettingsController::ATT_COUNTRYS] as $country) : ?>
            		<tr>
            			<td><?php echo $country->id; ?></td>
            			<td><?php echo htmlspecialchars($country->name); ?></td>
            			<td><?php echo htmlspecialchars($country->abbreviation); ?></td>
            			
            			<td class="success">
            				<a class="btn btn-xs btn-primary" href="/root/countrys/<?php echo  $country->id; ?>/update.html">Update</a>
            			</td>
            		</tr>
            		<?php endforeach;?>
            		
        		</tbody>
        	</table>
        </section>
    	<?php } else {?>
    	<div class="alert alert-danger">
    		<p>No countrys in database configuration</p>
    	</div>
    	<?php }?>
    </div>
</div>