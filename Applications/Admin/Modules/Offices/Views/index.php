<?php
use Applications\Admin\Modules\Offices\OfficesController;
?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'grid')? "active" : ""); ?>">
					<a href="/admin/offices/grid.html"><span class="glyphicon glyphicon-th-large"></span> Grid</a>
				</li>
				<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'table')? "active" : ""); ?>">
					<a href="/admin/offices/table.html"><span class="fa fa-table"></span> Table</a>
				</li>
			</ul>
			<?php if (!isset($_GET['affichage']) || $_GET['affichage'] == 'grid') { ?>
			<div class="panel-body">
				<div class="row">
				    <div class="col-lg-push-0 col-lg-12 col-md-push-0 col-md-12 col-sm-push-0 col-sm-12 col-xs-10 col-xs-push-1" style="padding-top: 20px;">
				    
				    	<?php if (!empty($_REQUEST[OfficesController::ATT_OFFICES])) { ?>
				    	<div class="row">
				    		<?php  foreach ($_REQUEST[OfficesController::ATT_OFFICES] as $office) : ?>
	        				<?php if ($office->central) continue; ?>
				    		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				        		<div class="panel panel-default">
				        			<div class="panel-heading">
				        				<a class="panel-title" href="/admin/offices/<?php echo  $office->id; ?>/">
						        			<strong style="font-size: 1.6rem;" class=""><?php echo htmlspecialchars("{$office->name}"); ?></strong>
				        				</a>        				
				        			</div>
				        			<div class="panel-body">
						        		<a class="thumbnail" href="/admin/offices/<?php echo  $office->id; ?>/">
						        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($office->name); ?>" src="/<?php echo ($office->photo); ?>">
						        			<?php if($office->member!=null ){ ?>
						        			<span class="label label-primary"style="">
						        				<span class="badge">
						        					<span class="fa fa-user"></span>
						        				</span>
						        				<?php echo htmlspecialchars("{$office->member->name} {$office->member->postName} {$office->member->lastName}"); ?>
					        				</span>
						        			<?php } else { ?>
						        			<span class="label label-success">
						        				<span class="badge">
						        					<span class="fa fa-home"></span>
						        				</span>
						        				Center
					        				</span>
						        			<?php } ?>
						        		</a>
						        		
						        		<div class="alert alert-info">
						        			<?php echo htmlspecialchars($office->officeSize!=null? $office->officeSize->size->name : "");?>
						        		</div>
				        			</div>
				        			<div class="panel-footer">
				        				<?php echo htmlspecialchars($office->localisation); ?>
				        			</div>
				        		</div>
				             </div>
				    		<?php endforeach;?>
				    	</div>
				    	<?php } ?>
				    </div>
				</div>
			</div>
			<?php } else {?>
			<section class="table-responsive">
	        	<table class="table table-bordered">
	        		<thead>
	        			<tr>
	        				<th>Photo</th>
	        				<th>Names</th>
	        				<th>Size</th>
	        				<th>Member ID</th>
	        				<th>Localisation</th>
	        				<th>Options</th>
	        			</tr>
	        		</thead>
	        		<tbody>
						<?php  foreach ($_REQUEST[OfficesController::ATT_OFFICES] as $office) : ?>
						<?php if ($office->central)  continue; ?>
    					<tr>
    						<td style="width: 30px;">
    							<img style="width: 30px;" src="/<?php echo ($office->photo);?>">
    						</td>
    						<td><?php echo htmlspecialchars($office->name);?></td>
    						<td><?php echo htmlspecialchars($office->officeSize!=null? $office->officeSize->size->name : "");?></td>
    						<td><?php echo ($office->member->matricule);?></td>
    						<td><?php echo htmlspecialchars($office->localisation); ?></td>
    						<td>
    							<a class="btn btn-primary" href="/admin/offices/<?php echo  $office->id; ?>/">
    								<span class="glyphicon glyphicon-folder-open"></span> Open
    							</a>
    							<a class="btn btn-success" href="/admin/offices/<?php echo  "{$office->id}/update.html"; ?>">
    								<span class="glyphicon glyphicon-edit"></span> update
    							</a>
    						</td>
    					</tr>
						<?php endforeach; ?>
	        		</tbody>
	        	</table>
	        </section>
			<?php }?>
		</div>
	</div>
</div>

			