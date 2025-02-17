<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\Request;
use PHPBackend\AppConfig;
use Core\Shivalik\Filters\SessionAdminFilter;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];

/**
 * @var GradeMember $gradeMember
 */
$gradeMember = $_REQUEST[MembersController::ATT_GRADE_MEMBER];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Withdrawal[] $operations
 */
$operations = $_REQUEST[MembersController::ATT_WITHDRAWALS];

$admin = $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION];
?>

<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="table table-bordered  panel panel-default">
        		<caption>Withdrawals controls</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Date end time</th>
        				<th>Office</th>
        				<th>Amount</th>
        				<th>Telephone</th>
        				<th class="text-center">Served</th>
        				<th class="text-center">Raport</th>
        				<th class="text-center">Option</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($_REQUEST[MembersController::ATT_WITHDRAWALS] as $w) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td><?php echo htmlspecialchars($w->dateAjout->format('d/m/Y \a\t H:i'));?></td>
    						<td> <?php echo htmlspecialchars($w->office->name);?></td>
    						<td><?php echo ("$w->amount {$config->get('devise')}");?></td>
    						<td><?php echo ($w->telephone);?></td>
    						
    						<th class="text-center text-<?php echo ($w->admin!=null? "success":"danger"); ?>">
    							<span class="glyphicon glyphicon-<?php echo ($w->admin!=null? "ok":"remove"); ?>"></span>
							</th>
							
							<th class="text-center text-<?php echo ($w->raport!=null? "success":"danger"); ?>">
    							<span class="glyphicon glyphicon-<?php echo ($w->raport!=null? "ok":"remove"); ?>"></span>
							</th>
    						
    						<td class="text-center">
    							<?php if ($w->admin==null && $admin->getOffice()->getId() == $w->office->id) : ?>
    							<a class="btn btn-danger" href="<?php echo "/admin/members/{$member->getId()}/withdrawals/{$w->id}.html"; ?>">
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