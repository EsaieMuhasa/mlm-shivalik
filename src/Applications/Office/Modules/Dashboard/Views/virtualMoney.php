<?php
use Applications\Office\Modules\Dashboard\DashboardController;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use Core\Shivalik\Filters\SessionOfficeFilter;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

$office = $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION]->getOffice();
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