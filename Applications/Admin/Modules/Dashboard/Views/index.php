<?php
use Applications\Admin\Modules\Dashboard\DashboardController;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-laptop"></i> <?php echo ($_REQUEST[DashboardController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
        <div class="row">
        	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="info-box green-bg">
                    <i class="fa fa-users"></i>
                    <div class="count"><?php echo ($_REQUEST[DashboardController::PARAM_MEMBER_COUNT]); ?></div>
                    <div class="title">Members</div>
                </div>
                <!--/.info-box-->
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="info-box green-bg">
                    <i class="fa fa-users"></i>
                    <div class="count"><?php echo ($_REQUEST[DashboardController::PARAM_UPGRADES_COUNT]); ?></div>
                    <div class="title">Upgrades</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
        
        <div class="row">
        	 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-inbox"></i>
                    <div class="count"><?php echo "{$_REQUEST[DashboardController::ATT_SOLDE]} {$config->get('devise')}"; ?></div>
                    <div class="title">Sold</div>
                </div>
                <!--/.info-box-->
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-money"></i>
                    <div class="count"><?php echo "{$_REQUEST[DashboardController::ATT_SOLDE_WITHDRAWALS]} {$config->get('devise')}"; ?></div>
                    <div class="title">Requested</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="glyphicon glyphicon-ok-circle"></i>
                    <div class="count"><?php echo "{$_REQUEST[DashboardController::ATT_SOLDE_WITHDRAWALS_SERVED]} {$config->get('devise')}"; ?></div>
                    <div class="title">Served</div>
                </div>
                <!--/.info-box-->
            </div>
            
        </div>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="panel palel-default">
			<div class="panel-heading">
				<strong class="panel-title">Membership by packet</strong>
			</div>
			<div class="panel-body graphic" data-config="/admin/dashboard/statistics/config-chart-packets.json">
				<canvas></canvas>
			</div>
			<div class="panel-footer">above the distribution of members for each pack</div>
		</div>
		
		<?php if ($_REQUEST[DashboardController::ATT_PURCHASE] > 0) : ?>
		<div class="panel palel-default">
			<div class="panel-heading">
				<strong class="panel-title"><span class="glyphicon glyphicon-warning-sign"></span> Purchase</strong>
			</div>
			<div class="panel-body">
				<div class="alert alert-info">
					<strong>Purchase of month of <?php $date = new DateTime(); echo $date->format("M Y") ?> </strong>
					<h3><?php echo $_REQUEST[DashboardController::ATT_PURCHASE]; ?> $</h3>
				</div>
			</div>
			<?php if ($_REQUEST[DashboardController::ATT_DISPTCH_PURCHASE]) : ?>
			<div class="panel-footer">
				<a href="/admin/dispatch-purchase-of-month.html" class="btn btn-danger">Dispatch</a>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php if (!empty($_REQUEST[DashboardController::ATT_WITHDRAWALS])): ?>
<div class="row">
    <div class="col-md-12">
        <section class="table-responsive">
        	<table class="table table-bordered panel panel-default">
        		<caption>Withdrawals requests</caption>
        		<thead class="panel-heading">
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
        		<tbody class="panel-body">
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
    							<a class="btn btn-danger" href="<?php echo "/admin/members/{$withdrowal->member->getId()}/withdrawals/{$withdrowal->id}.html"; ?>">
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
<?php endif;?>

<hr/>

<?php if (!empty($_REQUEST[DashboardController::ATT_RAPORT_WITHDRAWALS])) : ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table class="table table-bordered table-condenced panel panel-info">
    			<caption class="panel-title">Offices rapports</caption>
				<thead class="panel-heading">
					<tr>
						<th>Date</th>
						<th>Office</th>
						<th>Amount</th>
						<th>Options</th>
					</tr>
				</thead>
				<tbody class="panel-body">
					<?php foreach ($_REQUEST[DashboardController::ATT_RAPORT_WITHDRAWALS] as $raport) : ?>
					<tr>
						<td><?php echo $raport->dateAjout->format('D, d M Y \a\t H:i'); ?></td>
						<td></td>
						<td><?php //echo $raport->dateAjout->format('D, d M Y \a\t H:i'); ?></td>
						<td></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif; ?>
