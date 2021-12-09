<?php
use Library\Config;
use Entities\Office;
use Applications\Member\Modules\MyOffice\MyOfficeController;
use Applications\Member\MemberApplication;
use Entities\Withdrawal;

$config = Config::getInstance();

/**
 * @var Office $office
 */
$office = MemberApplication::getConnectedMember()->getOfficeAccount();

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[MyOfficeController::ATT_WITHDRAWALS];
?>


<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-users"></i>
            <div class="count"><?php echo ($_REQUEST[MyOfficeController::ATT_COUNT_MEMEBERS]); ?></div>
            <div class="title">Members</div>
        </div>
        <!--/.info-box-->
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-graduation-cap"></i>
            <div class="count"><?php echo ($office->countUpgrades()); ?></div>
            <div class="title">Upgrades</div>
        </div>
        <!--/.info-box-->
    </div>
</div>

<div class="row">
	 <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="glyphicon glyphicon-ok"></i>
            <div class="count"><?php echo "{$office->getAvailableVirtualMoney()} {$config->get('devise')}"; ?></div>
            <div class="title">virtual</div>
        </div>
        <!--/.info-box-->
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="fa fa-money"></i>
            <div class="count"><?php echo "{$office->getSoldRequestWithdrawals()} {$config->get('devise')}"; ?></div>
            <div class="title">Requested</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <?php if ($office->getSoldAcceptWithdrawals() > 0) : ?>
     <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="glyphicon glyphicon-ok-circle"></i>
            <div class="count"><?php echo "{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"; ?></div>
            <div class="title">Served</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endif;?>
    
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
    
    <?php if ($_REQUEST[MyOfficeController::ATT_CAN_SEND_RAPORT]) : ?>
    <div class="col-xs-12">
    	<div class="alert alert-info">
    		<h2 class="alert-title">Alert</h2>
    		<p>You have <?php echo "{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"; ?> that you have already made matched to the adhering members of the society. By clicking on the button below, the report will be sent directly to the hierarchy.</p>
    		<a class="btn btn-primary" href="/member/office/send-matched-money.html">
    			<span class="fa fa-send"></span> Send report
    		</a>
    	</div>
    </div>
    <?php endif; ?>
    
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
	        			<?php $num = 0; ?>
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
        </div>
        <?php endif;?>
    </div>
</div>
