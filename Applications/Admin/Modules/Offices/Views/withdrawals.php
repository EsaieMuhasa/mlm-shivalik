<?php 

use Core\Shivalik\Entities\Withdrawal;
use Applications\Admin\Modules\Offices\OfficesController;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use Core\Shivalik\Entities\Office;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

/**
 * @var Office[] $offices
 */
$offices = $_REQUEST[OfficesController::ATT_OFFICES];

$count = $_REQUEST[OfficesController::ATT_COUNT_WITHDRAWALS];//nombre max des operations

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[OfficesController::ATT_WITHDRAWALS];
$offset =  isset($_GET['offset'])? intval($_GET['offset'], 10) : 0;
?>
<?php if (!empty($withdrawals)) : ?>
<h2></h2>
<div class="panel panel-default table-responsive">
	<table class="table">
		<caption class="hidden">withdrawals</caption>
		<thead class="panel-heading">
			<tr>
				<th>N°</th>
				<th>Names</th>
				<th>Member ID</th>
				<th>Amount</th>
				<th>Telephone</th>
				<th>Served</th>
				<th>Redirecte</th>
				<th>Date and time</th>
			</tr>
		</thead>
		<tbody class="panel-body">
			<?php $num = 0; ?>
			<?php foreach ($withdrawals as $withdrawal): ?>
				<tr>
					<td><?php  $num++; echo ($num+$offset);?> </td>
					<td><?php echo htmlspecialchars($withdrawal->getMember()->getNames());?></td>
					<td><?php echo ($withdrawal->getMember()->getMatricule());?></td>
					<td><?php echo ("{$withdrawal->getAmount()} {$config->get('devise')}");?></td>
					<td><?php echo htmlspecialchars($withdrawal->getTelephone());?></td>
					<td class="<?php echo ($withdrawal->getAdmin()!=null? "text-success":"text-danger"); ?>">
						<span class="glyphicon glyphicon-<?php echo ($withdrawal->getAdmin()!=null? "ok":"remove"); ?>"></span>
					</td>
					<td>
						<?php if ($withdrawal->getAdmin() == null) : ?>
            			<a data-toggle="modal" class="btn btn-info" href="#redirect-matching<?php echo $withdrawal->getId(); ?>">See more</a>
						<?php endif; ?>
					</td>
					<td><?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    <?php if ($count > count($withdrawals)) : 
        $steep =  isset($_GET['limit'])? intval($_GET['limit'], 10) : intval($config->get('defaultLimit')->getValue(), 10); ?>
    <div class="panel-footer">
		<div class="">
			<?php for($i=0; $i<$count; $i += $steep) :  ?>
			<a href="<?php echo ($steep).'-'.($i); ?>.html" class="btn btn-<?php echo (((isset($_GET['offset']) && ($_GET['offset'] == ($i))) || (!isset($_GET['offset']) && $i==0))? 'danger':'primary'); ?>"><?php echo ($i/$steep); ?></a>
			<?php endfor; ?>
		
		</div>
    </div>
    <?php endif; ?>
</div>
<?php endif;?>


<?php foreach ($withdrawals as $withdrawal): ?>

<?php if ($withdrawal->getAdmin() != null) {continue;} ?>
<div class="modal fade" id="redirect-matching<?php echo $withdrawal->getId(); ?>">
	<div class="modal-dialog modal-lg" style="margin: auto;position: inherit;">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" data-dismiss="modal">x</button>
				<h4><?php echo htmlspecialchars($withdrawal->getMember()->getNames());?></h4>
			</div>
			<div class="modal-body" style="max-height: 350px; overflow: auto;">
				<p>
    				<?php echo htmlspecialchars($withdrawal->getMember()->getNames());?> wants to match<strong class="text-primary"> $ <?php echo $withdrawal->getAmount(); ?> </strong> in his account. the request was sent on <?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?>,
    				to <em><?php echo htmlspecialchars($office->getName()); ?></em> office.
				</p>
				<p>
					To redirect this operation, cauche another office and validate the operation
				</p>
				<section class="panel">
                    <header class="panel-heading">matching redirection form</header>
                    <div class="panel-body">
                    	<form action="<?php echo "/admin/offices/{$office->getId()}/withdrawals/{$withdrawal->getId()}/redirect.html";?>" method="post">
                    		<div class="form-group">
                    			<label class="">Pick one of the office below</label>
                    			<ul class="list list-group">
                        			<?php foreach ($offices as $of) : ?>
                        			<li class="list-group-item">
                        				<?php if ($of->getId() == $office->getId()) { ?>
                        				<span class="glyphicon glyphicon-ok"></span>
                        				<?php } else { ?>
                        				<input type="radio" class="" name="office" <?php echo ($of->getId() == $office->getId() ? 'checked="checked"':""); ?> value="<?php echo $of->getId(); ?>" id="radio-office<?php echo "{$of->getId()}_{$withdrawal->getId()}"; ?>"/>
                        				<?php } ?>
                        				<label for="radio-office<?php echo "{$of->getId()}_{$withdrawal->getId()}"; ?>" class="<?php echo ($of->getId() == $office->getId() ? 'text-primary':""); ?>">
                        					<?php  echo htmlspecialchars($of->getName()); ?>
                        				</label>
                        			</li>
                        			<?php endforeach;?>
                    			</ul>
                    		</div>
                    		<div class="">
                    			<button class="btn btn-primary">
                    				<span class="glyphicon glyphicon-ok"></span> Validate
                    			</button>
                    		</div>
                    	</form>
                    </div>
                </section>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" type="button" data-dismiss="modal">cancel</button>				
			</div>
		</div>
	</div>
</div>
<?php endforeach;?>