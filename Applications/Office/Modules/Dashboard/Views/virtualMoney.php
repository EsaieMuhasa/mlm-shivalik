<?php
use Library\Config;
use Applications\Office\OfficeApplication;
use Applications\Office\Modules\Dashboard\DashboardController;

$config = Config::getInstance();

$office = OfficeApplication::getConnectedUser()->getOffice();
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-laptop"></i> <?php echo ($_REQUEST[DashboardController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>
<hr/>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title">Matchings</h2>
	</div>
	<div class="panel-body">
		<div class="row">
			 <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box green-bg">
		            <i class="fa fa-money"></i>
		            <div class="count"><?php echo "{$office->getSoldRequestWithdrawals()} {$config->get('devise')}"; ?></div>
		            <div class="title">Requested</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    
		    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box blue-bg">
		            <i class="glyphicon glyphicon-ok"></i>
		            <div class="count"><?php echo ("{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"); ?></div>
		            <div class="title">served</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		</div>
		<?php if (!empty($_REQUEST[DashboardController::ATT_WITHDRAWALS])): ?>
		<div class="row">
		    <div class="col-xs-12">
				<div class="panel panel-default table-responsive">
		
		        	<table class="table table-bordered">
		        		<thead class="panel-heading">
		        			<tr>
		        				<th>N°</th>
		        				<th>Photo</th>
		        				<th>Names</th>
		        				<th>ID</th>
		        				<th>Telephone</th>
		        				<th>Amount</th>
		        				<th>State</th>
		        			</tr>
		        		</thead>
		        		<tbody>
		        			<?php $num = 0; ?>
							<?php foreach ($_REQUEST[DashboardController::ATT_WITHDRAWALS] as $withdrowal) : ?>
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
				</div>
		    </div>
		</div>
		<?php endif;?>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title">Virtual money</h2>
	</div>
	<div class="panel-body">
		<div class="row">
			 <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box blue-bg">
		            <i class="glyphicon glyphicon-ok"></i>
		            <div class="count"><?php echo "{$office->getAvailableVirtualMoney()} {$config->get('devise')}"; ?></div>
		            <div class="title">Available</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    
		    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box blue-bg">
		            <i class="glyphicon glyphicon-trash"></i>
		            <div class="count"><?php echo ("{$office->getUsedVirtualMoney()} {$config->get('devise')}"); ?></div>
		            <div class="title">trash</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    
		    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box blue-bg">
		            <i class="glyphicon glyphicon-retweet"></i>
		            <div class="count"><?php echo ("{$office->getSoldRetroCommission()} {$config->get('devise')}"); ?></div>
		            <div class="title">Afiliation</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    
		    <?php if ($office->hasDebts()) : ?>
		    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		        <div class="info-box red-bg">
		            <i class="glyphicon glyphicon-warning-sign"></i>
		            <div class="count"><?php echo ("{$office->getDebts()} {$config->get('devise')}"); ?></div>
		            <div class="title">Debts</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    <?php endif; ?>
		    
		</div>
	</div>
</div>