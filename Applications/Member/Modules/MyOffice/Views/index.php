<?php
use Applications\Member\Modules\MyOffice\MyOfficeController;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Filters\SessionMemberFilter;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
/**
 * @var Office $office
 */
$office = $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION]->officeAccount;

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
    
    <?php if ($office->getSoldAcceptWithdrawals() != 0) : ?>
    <div class="col-xs-12">
    	<div class="alert alert-info">
    		<strong class="alert-title"><span class="glyphicon glyphicon-ok"></span> Information</strong>
    		<p>You have <?php echo "{$office->getSoldAcceptWithdrawals()} {$config->get('devise')}"; ?> that you have already made matched to the adhering members of the society. By clicking on the button below, the report will be sent directly to the hierarchy.</p>
    		<a class="btn btn-primary" href="/member/office/send-matched-money.html">
    			<span class="fa fa-send"></span> Send report
    		</a>
    	</div>
    </div>
    <?php endif; ?>
</div>
