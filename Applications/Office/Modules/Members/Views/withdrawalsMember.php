<?php
use Library\Config;
use Applications\Office\Modules\Members\MembersController;
use Applications\Office\OfficeApplication;

/**
 * @var \Entities\Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var \Entities\Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];


$config = Config::getInstance();
?>

<?php require_once '_nav-member.php';?>

<?php if (!empty($_REQUEST[MembersController::ATT_WITHDRAWALS])) : ?>
<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="panel panel-default table table-bordered">
        		<caption>Withdrawals controls</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Date end time</th>
        				<th>Request ID</th>
        				<th>Amount</th>
        				<th class="text-center">status</th>
        				<th class="text-center">Option</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($_REQUEST[MembersController::ATT_WITHDRAWALS] as $w) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td><?php echo htmlspecialchars($w->dateAjout->format('d/m/Y \a\t H:i'));?></td>
    						<td> <?php echo ($w->id);?></td>
    						<td><?php echo ("$w->amount {$config->get('devise')}");?></td>
    						<td><?php echo ($w->telephone);?></td>
    						
    						<th class="text-center">
    							<span class="glyphicon glyphicon-<?php echo ($w->admin!=null? "ok":"remove"); ?>"></span>
							</th>
    						
    						<td class="text-center">
    							<?php if ($w->admin==null && OfficeApplication::getConnectedUser()->getOffice()->getId() == $w->office->id) : ?>
    							<a class="btn btn-danger" href="<?php echo "/office/members/{$member->getId()}/withdrawals/{$w->id}.html"; ?>">
    								<span class="glyphicon glyphicon-ok"></span> Accept
    							</a>
    							<?php endif; ?>
    						</td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
    </div>
</div>
<?php endif; ?>