<?php 
use Core\Shivalik\Entities\MonthlyOrder;
use Applications\Office\Modules\Products\ProductsController;
use PHPBackend\Request;
use PHPBackend\AppConfig;

/**
 * @var MonthlyOrder[] $orders
 * @var AppConfig $config
 */
$orders = $_REQUEST[ProductsController::ATT_MONTHLY_ORDERS];
$count = $_REQUEST[ProductsController::ATT_MONTHLY_ORDERS_COUNT];
$config = $_REQUEST[Request::ATT_APP_CONFIG];

$defaultLimit = intval($config->get('defaultLimit')->getValue(), 10);
$defaultLimit = isset($_GET['limit'])? $_GET['limit'] : $defaultLimit;
$offset = isset($_GET['offset'])? $_GET['offset'] : 0;
?>

<div class="text-right">
	<a class="btn btn-primary" href="/office/products/purchase/add.html">
		<span class="fa fa-plus"></span> New monthly order
	</a>
</div>

<?php if(!empty($orders)) : ?>
<div class="table-responsive">
	<table class="table table-bordered panel panel-default">
		<thead class="panel-heading">
			<tr>
				<th>Member ID:</th>
				<th>Member names</th>
				<th>Amount</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody class="panel-body">
			<?php foreach ($orders as $order) : ?>
			<tr>
				<td><?php echo $order->getMember()->getMatricule(); ?></td>
				<td><?php echo htmlspecialchars($order->getMember()->getNames()); ?></td>
				<td><?php echo htmlspecialchars("{$order->getManualAmount()} {$config->get('devise')}"); ?></td>
				<td><?php echo htmlspecialchars($order->getFormatedDateAjout('d/m/Y \a\t H:i:s')); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php if ($count > $defaultLimit) : ?>
<div>
	<?php for ($i = 0, $step = 0; $step < $count; $step += $defaultLimit) : ?>
	<a class="btn btn-<?php echo $step == $offset? 'danger' :'primary'; ?>" href="/office/products/purchase/<?php echo "{$defaultLimit}-{$step}"; ?>.html">
		<?php echo ($i++); ?>
	</a>
	<?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>