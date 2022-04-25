<?php 
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product $product
 * @var Product[] $products
 */
$product = $_REQUEST[ProductsController::ATT_PRODUCT];
?>
<section class="row">
	<div class="col-xs-12">
		<div class="thumbnail">
    		<h2><?php echo htmlspecialchars($product->name); ?></h2>
    		<div class="row">
    			<div class="col-xs-12 col-sm-8 col-md-8">
            		<div class="alert alert-info">
            			<span class="label label-info">Defualt unit price: <?php echo $product->defaultUnitPrice; ?> $</span>, <span class="badge"><?php echo $product->packagingSize; ?></span>, record date <?php echo $product->dateAjout->format('D, d M Y \a\t H:i:s')?>
            		</div>
            		<div class="text-justify">
            			<p><?php echo htmlspecialchars($product->description); ?></p>
            		</div>
            		
            		<div>
            			<a href="update.html" class="btn btn-primary">Update</a>
            			<a href="stocks/add.html" class="btn btn-info">new stocks</a>
            		</div>
            		<br>
            		
    			</div>
    			<div class="col-md-4 col-sm-4 col-xs-12">
    				<img class="thumbnail" src="/<?php echo $product->picture;?>" alt="<?php echo htmlspecialchars($product->name); ?>"/>
    			</div>
    			
    			<?php if($product->hasStock()) : ?>
    			<div class="col-xs-12">
            		<div class="panel panel-default">
            			<div class="panel-heading">
            				<strong class="panel-title">Stocks</strong>
            			</div>
            			<div class="panel-body">
            				<ul class="list-group">
                				<?php foreach ($product->getStocks() as $stock) : ?>
            					<li class="list-group-item">
            						<h4><?php echo $stock->getFormatedDateAjout(); ?></h4>
            						<div class="row">
            							<div class="col-xs-8 col-sm-9 col-md-10">
                                			<div class="progress">
                                            	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo ($stock->getSoldToPercent()); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($stock->getSoldToPercent()); ?>%;"><?php echo ($stock->getSoldToPercent()); ?>% (<?php echo ("{$stock->getSold()}/{$stock->getQuantity()}"); ?>)</div>
                                            </div>        					
            							</div>
            							<div class="col-xs-4 col-sm-3 col-md-2 text-right">
            								<a href="stocks/<?php echo $stock->id; ?>/update.html" class="btn btn-xs btn-primary">
            									<span class="fa fa-edit"></span><span class="-hidden-xs">Update</span>
            								</a>
            								<a href="stocks/<?php echo $stock->id; ?>/delete.html" class="btn btn-xs btn-danger">
            									<span class="glyphicon glyphicon-remove-sign"></span><span class="-hidden-xs">Delete</span>
            								</a>
            							</div>
            						</div>
            					</li>
                                <?php endforeach; ?>
            				</ul>
            			</div>
            		</div>
    			</div>
    			<?php endif;?>
    			
    		</div>
		</div>
	</div>
</section>