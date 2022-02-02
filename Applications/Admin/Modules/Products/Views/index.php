<?php
use PHPBackend\AppConfig;
use PHPBackend\Request;
use Applications\Admin\Modules\Products\ProductsController;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$productSteep = intval($config->get('productsSteep')->getValue(), 10);
$count = intval($_REQUEST[ProductsController::ATT_COUNT_PRODUCT], 10);

$offset = isset($_GET['offset'])? $_GET['offset'] : 0;
$affichage = $_REQUEST['affichage'];
$prev = $offset - $productSteep;
$next = $offset + $productSteep;
?>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ($affichage == 'grid'? "active" : ""); ?>">
					<a href="/admin/products/grid/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="glyphicon glyphicon-th-large"></span> Grid</a>
				</li>
				<li role="presentation" class="<?php echo (($affichage == 'table')? "active" : ""); ?>">
					<a href="/admin/products/table/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="fa fa-table"></span> Table</a>
				</li>
				
			</ul>
			<?php if ($affichage == 'grid') { ?>
			<div class="panel-body">
				
				<div class="row">
				    <div class="col-xs-12" style="padding-top: 20px;">
				    
				    	<?php if (!empty($_REQUEST[ProductsController::ATT_PRODUCTS])) { ?>
				    	<div class="row">
				    		<?php  foreach ($_REQUEST[ProductsController::ATT_PRODUCTS] as $product) : ?>
				    		<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
				    			<div class="panel panel-default">
				    				<div class="panel-heading">				    				
    				        			<strong class="panel-title"><?php echo htmlspecialchars("{$product->name}"); ?></strong>
				    				</div>
    				    			<div class="panel-body">
    				        			<img class="image-rond" style="width: 100%;" alt="picture of <?php echo htmlspecialchars($product->name); ?>" src="/<?php echo ($product->picture); ?>">
    				        			<label class="label label-info">
    				        				<?php echo htmlspecialchars("{$product->defaultUnitPrice}"); ?> $
    				        			</label>				    			
    				    			</div>
    				    			<div class="panel-footer">
    				        			<a class="btn btn-primary" href="/admin/products/<?php echo  $product->id; ?>/">See more</a>
    				        			<a class="btn btn-success" href="/admin/products/<?php echo  $product->id; ?>/update.html">Update</a>
    				    			</div>
				    			</div>
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
	    						<td><?php echo htmlspecialchars($product->name);?></td>
	    						<td>
	    							<a class="btn btn-primary" href="/admin/products/<?php echo  $product->id; ?>/">
	    								<span class="glyphicon glyphicon-comment"></span> See more
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
			<?php if ($productSteep < $count) : ?>
			<div class="panel-footer">
				<nav aria-label="products pager navigation">
                    <ul class="pagination">
                    	<?php if ($prev >=0 ) : ?>
                        <li class="previous">
                    		<a href="/admin/products/<?php echo "{$affichage}/{$productSteep}-{$prev}"; ?>.html">
                        		<span class="glyphicon glyphicon-fast-backward"></span>
                        	</a>
                    	</li>
                    	<?php endif;?>
                    	
                    	<?php $steep = 0; ?>
    					<?php for($i=0; $i<($count); $i += $productSteep) {  ?>
                        <li class="<?php echo ($offset == ($steep*$productSteep)? 'active':''); ?>">
        					<a href="<?php echo ($productSteep).'-'.($steep*$productSteep); ?>.html" ><?php echo ($steep); ?></a>
                        </li>
    					<?php $steep++;}?>                        
        				
        				<?php if ($next <= $count ) : ?>
                        <li class="next">
                        	<a href="/admin/products/<?php echo "{$affichage}/{$productSteep}-{$next}"; ?>.html">
                        		<span class="glyphicon glyphicon-fast-forward"></span>
                        	</a>
                    	</li>
                    	<?php endif; ?>
                    </ul>
                </nav>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
