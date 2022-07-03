<?php
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\Withdrawal;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

$requests = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEYS];

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[OfficesController::ATT_WITHDRAWALS];
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title">Users</strong>
	</div>
	<div class="panel-body">
        <div class="row">
        	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box green-bg">
                    <i class="fa fa-users"></i>
                    <div class="count"><?php echo ($_REQUEST[OfficesController::ATT_COUNT_MEMEBERS]); ?></div>
                    <div class="title">Members</div>
                </div>
                <!--/.info-box-->
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
		<strong class="panel-title">Withdrawals</strong>
	</div>
	<div class="panel-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-money"></i>
                    <div class="count"><?php echo "{$office->getSoldRequestWithdrawals()} {$config->get('devise')}"; ?></div>
                    <div class="title">Requested</div>
                </div>
                <!--/.info-box-->
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
		<strong class="panel-title">Virtual moneys</strong>
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
        </div>
	</div>
</div>
