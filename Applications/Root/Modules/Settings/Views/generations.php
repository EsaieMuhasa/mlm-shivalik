<?php
use Applications\Root\Modules\Settings\SettingsController;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-empire"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	
    	<div class="">
    		<a class="btn btn-primary" href="/root/generations/add.html"><span class="fa fa-plus"></span>News generation</a>
    	</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top: 20px;">
    
        <section class="thumbnail">
        	<?php if (!empty($_REQUEST[SettingsController::ATT_GENERATIONS])) { ?>
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
            		
            		<?php  foreach ($_REQUEST[SettingsController::ATT_GENERATIONS] as $generation) : ?>
            		<tr>
            			<td><?php echo $generation->number; ?></td>
            			<td><?php echo htmlspecialchars($generation->name); ?></td>
            			<td><?php echo htmlspecialchars($generation->abbreviation); ?></td>
            			<td><?php echo htmlspecialchars($generation->percentage); ?> % </td>
            			
            			<td class="success">
            				<a class="btn btn-xs btn-primary" href="/root/generations/<?php echo  $generation->id; ?>/update.html">Update</a>
            			</td>
            		</tr>
            		<?php endforeach;?>
            		
        		</tbody>
        	</table>
        	<?php } else {?>
        	<div class="alert alert-danger">
        		<p>No generation in database configuration</p>
        	</div>
        	<?php }?>
        </section>
    </div>
</div>