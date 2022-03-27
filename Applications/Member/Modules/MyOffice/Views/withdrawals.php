<?php 
use Applications\Member\Modules\MyOffice\MyOfficeController;
use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[MyOfficeController::ATT_WITHDRAWALS];

$steep = intval($config->get('limitCashout')->getValue(), 10);
$limit = $steep;
$offset = isset($_GET['offset'])? intval($_GET['offset']) : 0 ;

$count = $_REQUEST[MyOfficeController::ATT_COUNT_WITHDRAWALS];
?>

<div class="row">
	 <div class="col-xs-12">
    	<?php if (!empty($withdrawals)) : ?>
        <div class="panel panel-default">
	        <header class="panel-heading">
	        	<h2 class="panel-title">withdrawals</h2>
	        </header>
	        <section class="table-responsive">
	        	<table class="table">
	        		<caption></caption>
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>Amount</th>
	        				<th>Served</th>
	        				<th>date and time</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<?php $num = $offset; ?>
						<?php foreach ($withdrawals as $withdrawal): ?>
	    					<tr>
	    						<td><?php  $num++; echo ($num);?> </td>
	    						<td><?php echo htmlspecialchars($withdrawal->getMember()->getNames());?></td>
	    						<td><?php echo ($withdrawal->getMember()->getMatricule());?></td>
	    						<td><?php echo ("{$withdrawal->getAmount()} {$config->get('devise')}");?></td>
	    						<td class="<?php echo ($withdrawal->getAdmin()!=null? "text-success":"text-danger"); ?>">
	    							<span class="glyphicon glyphicon-<?php echo ($withdrawal->getAdmin()!=null? "ok":"remove"); ?>"></span>
	    						</td>
	    						<td><?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
	    					</tr>
						<?php endforeach; ?>
	        		</tbody>
	        	</table>
	        </section>
	        <?php if ($steep < $count) : ?>
	        <footer class="panel-footer">
	        	<?php for ($i = $steep; $i<$count; $i += $steep) : ?>
	        	<a href="<?php echo ("{$limit}-".($i-$steep)); ?>.html" class="btn btn-<?php echo (($i-$steep) == $offset? 'danger':'primary'); ?>">
	        		<?php echo ($i/$steep); ?>
	        	</a>
	        	<?php endfor; ?>
	        </footer>
	        <?php endif; ?>
        </div>
        <?php endif;?>
    </div>
</div>