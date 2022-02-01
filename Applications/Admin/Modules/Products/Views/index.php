<?php
use PHPBackend\AppConfig;
use PHPBackend\Request;
use Applications\Admin\Modules\Products\ProductsController;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$maxMembers = intval($config->get('maxMembers')->getValue(), 10);
$max = intval($_REQUEST[ProductsController::ATT_COUNT_PRODUCT], 10);

$offset = isset($_GET['offset'])? $_GET['offset'] : 0;

?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-leaf"></i> <?php echo ($_REQUEST[ProductsController::ATT_VIEW_TITLE]); ?>
    		<span class="badge"><?php echo (10); ?></span>
    	</h3>
    </div>
    <div class="col-xs-12 col-sm-2 col-md-1">
    	<a href="/admin/products/add.html" class="btn btn-primary btn-block">
    		<span class="fa fa-plus"></span> Add
    	</a>
    </div>
    <?php if ($maxMembers < $max) { ?>
	<div class="col-xs-12 col-sm-3 col-md-5">		
        <div class="btn-group">
        	<?php if (isset($_GET['offset']) && ($_GET['offset']-$maxMembers)>=0){ ?>
        	<a href="<?php echo ($maxMembers).'-'.(isset($_GET['offset'])? ($_GET['offset']-$maxMembers): 0); ?>.html" class="btn btn-info"><span class="glyphicon glyphicon-step-backward"></span>Prev</a>
        	<?php }?>
        	
        	<?php if (!isset($_GET['offset']) || (isset($_GET['offset']) && (($_GET['offset']+$maxMembers) <= ($max)))){ ?>
        	<a href="<?php echo ($maxMembers).'-'.(isset($_GET['offset'])? ($_GET['offset']+$maxMembers): ($maxMembers)); ?>.html" class="btn btn-primary">Next<span class="glyphicon glyphicon-step-forward"></span></a>
        	<?php } ?>
        </div>
	</div>
    <?php } ?>
	<div class="col-xs-12 col-sm-7 col-md-6 <?php echo (($maxMembers < $max)? '':'col-xs-offset-0 col-sm-offset-3 col-md-offset-5') ?>">
		<form action="" method="post">
			<div class="input-group">
    			<input type="search" name="index" value="<?php echo htmlspecialchars(isset($_REQUEST['index'])? $_REQUEST['index']:'');?>" id="amount-office" class="form-control" placeholder="put here search index" autocomplete="off"/>
				<span class="input-group-btn">
					<button class="btn btn-primary">Go</button>
				</span>
			</div>
		</form>
	</div>
</div>
<hr/>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'grid')? "active" : ""); ?>">
					<a href="/admin/products/grid/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="glyphicon glyphicon-th-large"></span> Grid</a>
				</li>
				<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'table')? "active" : ""); ?>">
					<a href="/admin/products/table/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="fa fa-table"></span> Table</a>
				</li>
			</ul>
			<?php if (isset($_GET['affichage']) && $_GET['affichage'] == 'grid') { ?>
			<div class="panel-body">
				<div class="row">
				    <div class="col-lg-push-0 col-lg-12 col-md-push-0 col-md-12 col-sm-push-0 col-sm-12 col-xs-10 col-xs-push-1" style="padding-top: 20px;">
				    
				    	<?php if (!empty($_REQUEST[ProductsController::ATT_PRODUCTS])) { ?>
				    	<div class="row">
				    		<?php  foreach ($_REQUEST[ProductsController::ATT_PRODUCTS] as $product) : ?>
				    		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
				        		<a class="thumbnail" href="/admin/products/<?php echo  $product->id; ?>/">
				        			<strong style="font-size: 2rem;" class="">ID: <?php echo htmlspecialchars("{$product->matricule}"); ?></strong>
				        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($product->name); ?>" src="/<?php echo ($product->photo); ?>">
				        			<em class="label label-<?php echo ($product->enable? 'primary':'danger') ?>"style=""><?php echo htmlspecialchars("{$product->name} {$product->postName} {$product->lastName}"); ?></em>
				        		</a>
				             </div>
				    		<?php endforeach;?>
				    	</div>
				    	<?php } else {?>
				    	<div class="alert alert-danger">
				    		<p>No product in database </p>
				    	</div>
				    	<?php }?>
				    </div>
				</div>
			</div>
			<?php } else { ?>
			<section class="table-responsive">
	        	<table class="table">
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Photo</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>Username</th>
	        				<th>packet</th>
	        				<th>Options</th>
	        				<th>Creation date</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<?php $num = 0; ?>
						<?php foreach ($_REQUEST[ProductsController::ATT_PRODUCTS] as $product): ?>
	    					<tr>
	    						<td><?php 
	    						$num++;
	    						echo ($num+$offset);?>
	    						</td>
	    						<td style="width: 30px;">
	    							<img style="width: 30px;" src="/<?php echo ($product->picture);?>">
	    						</td>
	    						<td><?php echo htmlspecialchars($product->names);?></td>
	    						<td><?php echo ($product->matricule);?></td>
	    						<td><?php echo ($product->pseudo);?></td>
	    						<td title="<?php echo htmlspecialchars($product->packet->grade->name);?>">
	    							<img style="width: 30px;" alt="<?php echo htmlspecialchars($product->packet->grade->name);?>" src="/<?php echo htmlspecialchars($product->packet->grade->icons->getXs());?>"/>
	    						</td>
	    						<td>
	    							<a class="btn btn-primary" href="/admin/products/<?php echo  $product->id; ?>/">
	    								<span class="glyphicon glyphicon-user"></span> Show acount
	    							</a>
	    							<a class="btn btn-success" href="/admin/products/<?php echo  "{$product->id}/update.html"; ?>">
	    								<span class="glyphicon glyphicon-edit"></span> update
	    							</a>
	    						</td>
	    						<td><?php echo ($product->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
	    					</tr>
						<?php endforeach; ?>
	        		</tbody>
	        	</table>
	        </section>
			<?php } ?>
			<div class="panel-footer">
				<?php if ($maxMembers < $max) { ?>
				<div class="">
				
					<?php
					$steep = 0;
					for($i=0; $i<($max); $i += $maxMembers) {  ?>
					<a href="<?php echo ($maxMembers).'-'.($steep*$maxMembers); ?>.html" class="btn btn-<?php echo (((isset($_GET['offset']) && ($_GET['offset'] == ($steep*$maxMembers))) || (!isset($_GET['offset']) && $steep==0))? 'danger':'primary'); ?>"><?php echo ($steep); ?></a>
					<?php $steep++;}?>
				
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
