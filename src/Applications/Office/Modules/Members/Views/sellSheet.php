<?php 

use Applications\Office\Modules\Members\MembersController;
use Core\Shivalik\Entities\SellSheetRow;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var SellSheetRow [] $rows
 */
$rows = $_REQUEST[MembersController::ATT_SELL_SHEET_ROWS];
?>
<div class="text-right">
    <a href="<?php echo "/office/members/{$member->id}/sell-sheet/add.html"; ?>" class="btn btn-primary">
        <span class="fa fa-plus"></span> New sell sheet row
    </a>
    <hr/>
</div>

<?php require_once dirname(__DIR__).DIRECTORY_SEPARATOR."Templates".DIRECTORY_SEPARATOR."monthly.php"; ?>

<?php if (!empty($rows)) : ?>
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
<?php endif; 
  