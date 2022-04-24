<?php 
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Stock;

/**
 * @var Stock[] $stocks
 */
$stocks = $_REQUEST[ProductsController::ATT_STOCKS];
?>

<div class="panel panel-default">
	<ul class="panel-heading nav nav-tabs">
		<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'grid')? "active" : ""); ?>">
			<a href="/admin/products/stocks/">
				<span class="glyphicon glyphicon-ok"></span> Availlable
			</a>
		</li>
		<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'table')? "active" : ""); ?>">
			<a href="/admin/products/stocks/all/">
				<span class="fa fa-table"></span> All stocks
			</a>
		</li>
	</ul>
	<div class="panel-body">
		<?php foreach ($stocks as $stock) : ?>
		<ul class="list-group">
			<li class="list-group-item">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-9">					
        				<h3><?php echo "{$stock->getProduct()->getName()}"; ?></h3>
        				<p class="text-info">
        					<strong class="text-danger">Status: <?php echo "{$stock->getSold()} / {$stock->getQuantity()} PCS ({$stock->getSoldToPercent()} % )"; ?> </strong><br/>
        					<i>
        						Record date <?php echo "{$stock->getFormatedDateAjout('d/m/Y \a\t H\h:i')}"; ?>. 
        						Manifacturing date: <?php echo ($stock->getManufacturingDate()->format('d/m/Y')); ?>, 
        						<span class="">
            						Exp: <?php echo ($stock->getExpiryDate()->format('d/m/Y')); ?>
        						</span>
        					</i><br/>
        				</p>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-3 text-right">
						<div class="btn-group">
    						<a href="stocks/<?php echo $stock->id; ?>/update.html" class="btn btn-xs btn-primary">
    							<span class="fa fa-edit"></span><span class="-hidden-xs">Update</span>
    						</a>
    						<?php if($stock->getSold() == $stock->getQuantity()) : ?>
    						<a href="stocks/<?php echo $stock->id; ?>/delete.html" class="btn btn-xs btn-danger">
    							<span class="glyphicon glyphicon-remove-sign"></span><span class="-hidden-xs">Delete</span>
    						</a>
    						<?php endif; ?>
						</div>
					</div>
					<div class="col-xs-12">
            			<div class="progress" style="height: 5px;">
                        	<div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="<?php echo ($stock->getSoldToPercent()); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($stock->getSoldToPercent()); ?>%;"><?php echo ($stock->getSoldToPercent()); ?>% (<?php echo ("{$stock->getSold()}/{$stock->getQuantity()}"); ?>)</div>
                        </div>        					
					</div>
				</div>
				<div class="row">
					<?php foreach ($stock->getAuxiliaries() as $aux) : ?>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="thumbnail">
							<strong class="text-primary"><?php echo htmlspecialchars($aux->getOffice()->getName()); ?></strong>
                			<div class="progress" style="height: 5px;">
                            	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo ($aux->getSoldToPercent()); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($aux->getSoldToPercent()); ?>%;"><?php echo ($aux->getSoldToPercent()); ?>% (<?php echo ("{$aux->getSold()}/{$aux->getQuantity()}"); ?>)</div>
                            </div>
                            <p class="text-info">
            					<strong class="text-danger">Status: <?php echo "{$aux->getSold()} / {$aux->getQuantity()} PCS ({$aux->getSoldToPercent()} % )"; ?></strong><br/>
            					<i>
            						Record date <?php echo "{$aux->getFormatedDateAjout('d/m/Y \a\t H\h:i')}"; ?>. 
            					</i>
            				</p>      					
						</div>
					</div>
                	<?php endforeach; ?>
				</div>
			</li>
		</ul>
		<?php endforeach;?>
	</div>
</div>