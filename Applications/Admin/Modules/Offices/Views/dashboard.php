<?php
use Applications\Admin\Modules\Offices\OfficesController;
use Library\Config;
use Entities\Office;
use Entities\Withdrawal;

$config = Config::getInstance();

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

/**
 * @var Office[] $offices
 */
$offices = $_REQUEST[OfficesController::ATT_OFFICES];

$requests = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEYS];

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[OfficesController::ATT_WITHDRAWALS];
?>


<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-users"></i>
            <div class="count"><?php echo ($_REQUEST[OfficesController::ATT_COUNT_MEMEBERS]); ?></div>
            <div class="title">Members</div>
        </div>
        <!--/.info-box-->
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-graduation-cap"></i>
            <div class="count"><?php echo ($office->countUpgrades()); ?></div>
            <div class="title">Upgrades</div>
        </div>
        <!--/.info-box-->
    </div>
</div>
<hr/>
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
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="glyphicon glyphicon-ok-circle"></i>
            <div class="count"><?php echo "{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"; ?></div>
            <div class="title">Served</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php if ($office->getSoldDebt()>0) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box red-bg">
            <i class="glyphicon glyphicon-info-sign"></i>
            <div class="count"><?php echo ("{$office->getSoldDebt()} {$config->get('devise')}"); ?></div>
            <div class="title">Debts membership</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endif; ?>
    
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
    
    <?php if (!empty($requests)) : ?>
    <?php foreach ($requests as $request) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box red-bg">
            <i class="glyphicon glyphicon-time"></i>
            <div class="count"><?php echo ("{$request->getAmount()} {$config->get('devise')}"); ?></div>
            <div class="title">Request</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="col-xs-12">
    	<?php if (!empty($withdrawals)) : ?>
        <div class="panel panel-default table-responsive">
	        <header class="panel-heading">
	        	<h2 class="panel-title">withdrawals</h2>
	        </header>
	        <section class="">
	        	<table class="table">
	        		<caption></caption>
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>Amount</th>
	        				<th>Telephone</th>
	        				<th>Served</th>
	        				<th>Redirecte</th>
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
	        </section>
        </div>
        <?php endif;?>
    </div>
</div>

<?php foreach ($withdrawals as $withdrawal): ?>

<?php if ($withdrawal->getAdmin() != null) {
    continue;
} ?>
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
