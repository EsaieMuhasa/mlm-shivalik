
<?php

use Applications\Office\Modules\Members\MembersController;

if (isset($_REQUEST[MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT]) &&  !isset($_REQUEST[MembersController::ATT_SPONSOR])) : ?>
<div class="row">
    <?php 
    /**
     * @var MonthlyOrder $monthly
     */
    $monthly = $_REQUEST[MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT]; ?>
	<div class="col-xs-12">
		<div class="alert alert-info">
			<h3>Purchase accounting for the month of <?php echo $monthly->getFormatedDateAjout("F Y") ?> </h3>
			<table class="table table-bordered table-condansed">
				<tbody>
					<tr>
						<td>Amount realize </td>
						<td><?php echo $monthly->getAmount(); ?> USD</td>
					</tr>
					<tr>
						<td>Used amount</td>
						<td><?php echo $monthly->getUsed(); ?> USD</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th>Available amount</th>
						<th><?php echo $monthly->getAvailable(); ?> USD</th>
					</tr>
				</tfoot>
			</table>
			<?php if($monthly->getAvailable() >= 50) : ?>
			<a class="btn btn-primary" href="<?php echo "/office/members/{$monthly->member->id}/affiliate.html"; ?>">
				Affiliate a new member
			</a>
			<a class="btn btn-info" href="<?php echo "/office/members/{$monthly->member->id}/pv-upgrade.html"; ?>">
				Updagrade account
			</a>
			<?php endif; ?>
		</div>
		
	</div>
</div>
<?php endif; ?>