<?php
use Applications\Admin\Modules\Members\MembersController;
use Applications\Office\Modules\Dashboard\DashboardController;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Entities\Office;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Office $office
 */
$office = $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION]->getOffice();
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-laptop"></i> <?php echo ($_REQUEST[MembersController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>
<hr/>
<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title">Count of operations on adhering members</h2>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
		        <div class="info-box green-bg">
		            <i class="fa fa-users"></i>
		            <div class="count"><?php echo ($_REQUEST[DashboardController::PARAM_MEMBER_COUNT]); ?></div>
		            <div class="title">Members</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		    <div class="col-sm-6 col-xs-12">
		        <div class="info-box green-bg">
		            <i class="fa fa-graduation-cap"></i>
		            <div class="count"><?php echo ($office->countUpgrades()); ?></div>
		            <div class="title">Upgrades</div>
		        </div>
		        <!--/.info-box-->
		    </div>
		</div>
		
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title">matching operations</h2>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="info-box blue-bg">
					<i class="fa fa-money"></i>
					<div class="count"><?php echo "{$office->getSoldRequestWithdrawals()} {$config->get('devise')}"; ?></div>
					<div class="title">Requested</div>
				</div>
				<!--/.info-box-->
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="info-box blue-bg">
					<i class="glyphicon glyphicon-ok-circle"></i>
					<div class="count"><?php echo "{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"; ?></div>
					<div class="title">Served</div>
				</div>
				<!--/.info-box-->
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="panel-title">Virtual money</h2>
	</div>
	<div class="panel-body">
		<div class="row">
			 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-leaf"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available product account</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-user"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available membership account</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Product</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Membership</div>
                </div>
                <!--/.info-box-->
            </div>
		</div>
	</div>
</div>

<?php if (!empty($_REQUEST[MembersController::ATT_WITHDRAWALS])): ?>
<div class="row">
    <div class="col-md-12">
		<div class="panel panel-default  table-responsive">

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
					<?php foreach ($_REQUEST[MembersController::ATT_WITHDRAWALS] as $withdrowal) : ?>
    					<tr>
    						<td><?php echo (++$num);?></td>
    						<td style="width: 30px;">
    							<img style="width: 30px;" src="/<?php echo ($withdrowal->memberPhoto);?>">
    						</td>
    						<td><?php echo htmlspecialchars($withdrowal->memberNames);?></td>
    						<td><?php echo ($withdrowal->memberMatricule);?></td>
    						<td><?php echo ($withdrowal->telephone);?></td>
    						<td><?php echo ($withdrowal->amount);?> $</td>
    						<td>
    							<?php if ($withdrowal->admin == null) : ?>
    							<a class="btn btn-danger" href="<?php echo "/office/members/{$withdrowal->member->getId()}/withdrawals/{$withdrowal->id}.html"; ?>">
    								<span class="glyphicon glyphicon-ok"></span> Accept
    							</a>
    							<?php endif; ?>
    						</td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        		<tfoot class="panel-footer">
        			<tr>
        				<td colspan="7"><div>Withdrawals requests</div></td>
        			</tr>
        		</tfoot>
        	</table>
		</div>
        <section class="table-responsive">
        </section>
    </div>
</div>
<?php endif;?>