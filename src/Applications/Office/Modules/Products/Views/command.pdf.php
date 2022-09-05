<?php 

use Applications\Office\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Command;
use PHPBackend\Request;
use PHPBackend\AppConfig;

/**
 * @var Command $command
 * @var AppConfig $config
 */
$command = $_REQUEST[ProductsController::ATT_COMMAND];
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>

<div class="thumbnail">
	<div class="row">
		<div class="col-xs-12">
			<h3 style="margin: 0px; color: #FA5920;">SHIVALIK HERBALS INTERNATIONAL</h3>
			<strong>web site: <a href="https://www.shivalikherbals.org">https://www.shivalikherbals.org</a></strong>
			<br/><strong>Office adress: </strong>
			
			<p>			
    			<em>E-mail: <a href="mailto:shivalikhb@gmail.com">shivalikhb@gmail.com</a></em>
    			<strong style="float: right;">
    				<?php if ($command->getDeliveryDate() != null) : ?>
    				<img class="" alt="Shivalik" src="<?php echo "{$_SERVER['DOCUMENT_ROOT']}/Web/img/ok.png"; ?>"> 
    				<?php endif; ?>
    				Invoice N° <?php echo $command->id; ?>  
				</strong>
			</p>
			<p style="text-align: center;padding-top: 10px;border-top: 1px solid #d0d0d0;">
				<span style="display:inline-block;">ORG / Mr / Mlle : </span> <span style="display:inline-block;border-bottom: 2px solid dotted;"><?php echo htmlspecialchars($command->getMember()->getNames()); ?></span>
			</p>
		</div>
		<div class="col-xs-1" style="float: right;margin-right: 10px;">
			<img class="" style="size: 100%;" alt="Shivalik" src="<?php echo "{$_SERVER['DOCUMENT_ROOT']}/Web/logo-75x75.png"; ?>">
		</div>
	</div>
	<div style="border-bottom: 1px solid #010101;"></div>
	
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>N°</th>
				<th>Product</th>
				<th>Quantity</th>
				<th>Unit price</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($command->getProducts() as $key => $product) : ?>
			<tr>
				<td><?php echo $key+1; ?></td>
				<td><?php echo htmlspecialchars($product->product->name); ?></td>
				<td><?php echo htmlspecialchars($product->quantity); ?></td>
				<td><?php echo htmlspecialchars("{$product->stock->unitPrice} {$config->get('devise')}"); ?></td>
				<td><?php echo htmlspecialchars("{$product->amount} {$config->get('devise')}"); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
    			<th colspan="2">Total</th>
    			<th><?php echo ($command->getTotalQuantity()); ?></th>
    			<th><?php echo ("{$command->getTotalUnitPrice()} {$config->get('devise')}"); ?></th>
    			<th class="info text-primary"><?php echo ("{$command->getAmount()} {$config->get('devise')}"); ?></th>
			</tr>
		</tfoot>
	</table>
	
	<p class="text-right" style="font-style: italic;">
		Validate by Shivalik, <?php echo $command->getFormatedDateAjout('d M Y \a\t H:i:s'); ?>
	</p>
	
</div>
