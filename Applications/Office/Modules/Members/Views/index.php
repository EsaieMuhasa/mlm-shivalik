<?php
use Applications\Admin\Modules\Members\MembersController;
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-users"></i> <?php echo ($_REQUEST[MembersController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>
<hr/>

<div class="row">
	<div class="col-xs-12">
		<h2 class="">
			Requested withdrawals: <?php echo $_REQUEST[MembersController::ATT_SOLDE_WITHDRAWALS]; ?>$
		</h2>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="table table-bordered">
        		<caption></caption>
        		<thead>
        			<tr>
        				<th>N°</th>
        				<th>Photo</th>
        				<th>Names</th>
        				<th>ID</th>
        				<th>Téléphone</th>
        				<th>Amount</th>
        				<th>State</th>
        			</tr>
        		</thead>
        		<tbody>
        			<?php $num = 0; ?>
					<?php foreach ($_REQUEST[MembersController::ATT_WITHDRAWALS] as $withdrowal) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td style="width: 30px;">
    							<img style="width: 30px;" src="/<?php echo ($withdrowal->member->photo);?>">
    						</td>
    						<td><?php echo htmlspecialchars($withdrowal->member->names);?></td>
    						<td><?php echo ($withdrowal->member->matricule);?></td>
    						<td><?php echo ($withdrowal->telephone);?></td>
    						<td><?php echo ($withdrowal->amount);?> $</td>
    						<td>
    							<a class="btn btn-danger" href="<?php echo "/office/members/{$withdrowal->member->getId()}/withdrawals/{$withdrowal->id}.html"; ?>">
    								<span class="glyphicon glyphicon-ok"></span> Accept
    							</a>
    						</td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
    </div>
</div>