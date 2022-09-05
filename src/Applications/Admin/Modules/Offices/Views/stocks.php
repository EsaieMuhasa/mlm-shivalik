<?php
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\AuxiliaryStock;

/**
 * @var AuxiliaryStock[] $stocks
 */
$stocks = $_REQUEST[ProductsController::ATT_STOCKS];
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title">Stocks</strong>
	</div>
	<div class="panel-body">
		<ul class="list-group">
			<?php foreach ($stocks as $stock) : ?>
			<li class="list-group-item">
				<h4><strong><?php echo htmlspecialchars($stock->getProduct()->getName())?></strong> <small class="text-info pull-right"><?php echo "{$stock->dateAjout->format('d/m/Y \a\t H:i:s')}"; ?></small></h4>

				<div class="row">
					<div class="col-xs-12 col-md-8 col-lg-9">
            			<div class="progress">
                        	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo ($stock->getSoldToPercent()); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($stock->getSoldToPercent()); ?>%;"><?php echo ($stock->getSoldToPercent()); ?>% (<?php echo ("{$stock->getSold()}/{$stock->getQuantity()}"); ?>)</div>
                        </div>        					
					</div>
					<div class="col-xs-12 col-md-4 col-lg-3 text-right">
						<div class="btn-group">
    						<a href="<?php echo $stock->id; ?>/commands/" class="btn btn-xs btn-info">
    							<span class="glyphicon glyphicon-ok"></span><span class="-hidden-xs"> See more</span>
    						</a>
    						<a href="<?php echo $stock->id; ?>/update.html" class="btn btn-xs btn-primary">
    							<span class="fa fa-edit"></span><span class="-hidden-xs"> Update</span>
    						</a>
    						<a href="<?php echo $stock->id; ?>/delete.html" class="btn btn-xs btn-danger">
    							<span class="glyphicon glyphicon-remove-sign"></span><span class="-hidden-xs"> Delete</span>
    						</a>
						</div>
					</div>
				</div>
			</li>
            <?php endforeach; ?>
		</ul>
	</div>
</div>