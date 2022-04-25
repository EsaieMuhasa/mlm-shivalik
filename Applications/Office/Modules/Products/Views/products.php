<?php 

use Applications\Office\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product[] $products
 */
$products = $_REQUEST[ProductsController::ATT_PRODUCTS];
?>

<?php if (!empty($products)) {?>
	<?php foreach ($products as $product) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong class="panel-title"><?php echo $product->name; ?></strong>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
							<img class="thumbnail" alt="<?php echo $product->name; ?>" src="/<?php echo $product->picture; ?>">
						</div>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
							<ul class="list-group">
    							<?php foreach ($product->getStocks() as $stock) : ?>
    							<li class="list-group-item">
            						<strong class=""><?php echo $stock->getFormatedDateAjout('d/m/Y \a\t H\h:i:s'); ?></strong>
                        			<div class="progress">
                                    	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo ($stock->getSoldToPercent()); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($stock->getSoldToPercent()); ?>%;"><?php echo ($stock->getSoldToPercent()); ?>% (<?php echo ("{$stock->getSold()}/{$stock->getQuantity()}"); ?>)</div>
                                    </div>
                                    <p class="text-danger">Expiry date: <?php echo $stock->getExpiryDate()->format('d/m/Y'); ?></p>
                                    <p class="">Manufacturing date: <?php echo $stock->getManufacturingDate()->format('d/m/Y'); ?></p>
            					</li>
    							<?php endforeach;?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
<?php } else { ?>
<div class="alert alert-danger">
	<strong>Alert</strong>
	<p>non valiable product stocks in this office</p>
</div>
<?php } ?>