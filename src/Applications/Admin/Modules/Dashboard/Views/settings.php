<?php
use Applications\Admin\Modules\Dashboard\DashboardController;
?>
<section class="panel panel-default">
	<header class="panel-heading">
		<h2 class="panel-title">Packects</h2>
	</header>
	<section class="panel-body">
    <?php if (!empty($_REQUEST[DashboardController::ATT_GRADES])) { ?>
    	<div class="row">
    		<?php  foreach ($_REQUEST[DashboardController::ATT_GRADES] as $grade) : ?>
    		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="padding-bottom: 30px;">
        		<div class="thumbnail">
        			<strong style="display: block;font-size: 2rem;"><?php echo htmlspecialchars($grade->name); ?></strong>
        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($grade->name); ?>" src="/<?php echo ($grade->icon); ?>">
        			<em class="badge"><?php echo htmlspecialchars($grade->percentage); ?>% </em>
        			<em class="label label-danger">up to <?php echo htmlspecialchars($grade->maxGeneration->name); ?></em>
        		</div>
             </div>
    		<?php endforeach;?>
    	</div>
	<?php } else {?>
    	<div class="alert alert-danger">
    		<p>No grade in database configuration</p>
    	</div>
	<?php }?>
	</section>
</section>

<section class="panel panel-default">
	<header class="panel-heading">
		<h2 class="panel-title">Office size configuration</h2>
	</header>
	<section class="panel-body">
    	<?php if (!empty($_REQUEST[DashboardController::ATT_SIZES])) { ?>
    	<table class="table table-bordered">
    		<caption class="hidden">Genegations registereds</caption>
    		<thead>
    			<tr>
    				<th>N°</th>
    				<th>Name</th>
    				<th>Abbreviation</th>
    				<th>Percentage</th>
    			</tr>
    		</thead>
    		<tbody>
        		
        		<?php  foreach ($_REQUEST[DashboardController::ATT_SIZES] as $key => $size) : ?>
        		<tr>
        			<td><?php echo ($key+1); ?></td>
        			<td><?php echo htmlspecialchars($size->name); ?></td>
        			<td><?php echo htmlspecialchars($size->abbreviation); ?></td>
        			<td><?php echo htmlspecialchars($size->percentage); ?> % </td>
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
</section>

<section class="panel panel-default">
	<header class="panel-heading">
		<h2 class="panel-title">Generations fonfiguration</h2>
	</header>
	<section class="panel-body">
		<?php if (!empty($_REQUEST[DashboardController::ATT_GENERATIONS])) { ?>
    	<table class="table table-bordered">
    		<caption class="hidden">Genegations registereds</caption>
    		<thead>
    			<tr>
    				<th>N°</th>
    				<th>Name</th>
    				<th>Abbreviation</th>
    				<th>Percentage</th>
    			</tr>
    		</thead>
    		<tbody>
        		
        		<?php  foreach ($_REQUEST[DashboardController::ATT_GENERATIONS] as $generation) : ?>
        		<tr>
        			<td><?php echo $generation->number; ?></td>
        			<td><?php echo htmlspecialchars($generation->name); ?></td>
        			<td><?php echo htmlspecialchars($generation->abbreviation); ?></td>
        			<td><?php echo htmlspecialchars($generation->percentage); ?> % </td>
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
</section>