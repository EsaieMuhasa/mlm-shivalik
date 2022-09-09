<?php

use Applications\Member\Modules\Account\AccountController;
use Applications\Office\Modules\Members\MembersController;
use Core\Shivalik\Entities\SellSheetRow;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var SellSheetRow [] $rows
 */
$rows = $_REQUEST[AccountController::ATT_SELL_SHEETS];
?>
<h1 class="text-primary">Your sell sheet</h1>
<?php if (!empty($rows)) { ?>
<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="panel table table-bordered">
        		<caption>Sell sheet</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Product</th>
        				<th>Quantity</th>
        				<th>Unit price</th>
        				<th>Total</th>
        				<th>Date</th>
        				<th>Office</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($rows as $row) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td><?php echo htmlspecialchars($row->product->name);?></td>
    						<td><?php echo ("{$row->quantity}");?></td>
    						<td><?php echo ("{$row->unitPrice} {$config->get('devise')}");?></td>
    						<td><?php echo ("{$row->totalPrice} {$config->get('devise')}");?></td>
    						<td><?php echo ($row->getFormatedDateAjout('d F Y \a\t H\h:i'));?></td>
    						<td><?php echo htmlspecialchars($row->office->name);?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
    </div>
</div>
<?php } else { ?>
<div class="alert alert-info">
	<p>no transaction on your sales sheet</p>
</div>
<?php } 


    