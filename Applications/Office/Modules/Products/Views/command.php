<?php 

use Applications\Office\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Command;
use PHPBackend\Request;
use PHPBackend\AppConfig;

/**
 * @var Command $command
 * @var AppConfig $config
 */
$command = $_SESSION[ProductsController::ATT_COMMAND];
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>

<div class="thumbnail">
	<div class="row">
		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3">
			<img class="thumbnail" alt="<?php echo htmlspecialchars($command->getMember()->getNames()); ?>" src="/<?php echo $command->getMember()->getPhoto(); ?>">
		</div>
		<div class="col-lg-6 col-md-6 col-sm-8 col-xs-7">
			<h4 class="text-primary"><?php echo htmlspecialchars($command->getMember()->getMatricule()); ?></h4>
			<p><?php echo htmlspecialchars($command->getMember()->getNames()); ?></p>
		</div>
	</div><div style="border-bottom: 1px solid #010101;"></div>
	
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
	
</div>

<div class="text-center">
	<a class="btn btn-primary" href="/office/products/command/validate.html">
		<span class="glyphicon glyphicon-ok"></span> Validate
	</a>
	<a class="btn btn-info" target="_black" href="/office/products/command/pdf.pdf">
		<span class="glyphicon glyphicon-print"></span> PDF
	</a>
	<a class="btn btn-danger" href="/office/products/command/cancel.html">
		<span class="glyphicon glyphicon-remove"></span> Cancel
	</a>
</div>