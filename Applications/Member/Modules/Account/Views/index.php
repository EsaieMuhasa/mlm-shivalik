<?php
use Applications\Member\MemberApplication;
use Applications\Member\Modules\Account\AccountController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use PHPBackend\Request;
use PHPBackend\AppConfig;
/**
 * @var Member $member
 */
$member = MemberApplication::getConnectedMember();

/**
 * @var Account $compte
 */
$compte = $_REQUEST[AccountController::ATT_ACCOUNT];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-home"></i> <?php echo ($_REQUEST[AccountController::ATT_VIEW_TITLE]); ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="fa fa-tag"></i>
            <div class="count"><?php echo ($compte->getLeftPv()); ?></div>
            <div class="title">Left PV</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="fa fa-tag"></i>
            <div class="count"><?php echo ($compte->getMiddlePv()); ?></div>
            <div class="title">MIDDLE PV</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="fa fa-tag"></i>
            <div class="count"><?php echo ($compte->getRightPv()); ?></div>
            <div class="title">Right PV</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box brown-bg">
            <i class="fa fa-tags"></i>
            <div class="count"><?php echo ($compte->getPv()); ?></div>
            <div class="title">TOTAL PV</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- montant disponible -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-money"></i>
            <div class="count"><?php echo ("{$compte->getSolde()} {$config->get("devise")}"); ?></div>
            <div class="title">Reg <?php echo htmlspecialchars("&"); ?>  Upg Wallet</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-link"></i>
            <div class="count">0<?php echo ("{$config->get("devise")}"); ?></div>
            <div class="title">Reachat</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-users"></i>
            <div class="count">0<?php echo ("{$config->get("devise")}"); ?></div>
            <div class="title">PVW</div>
        </div>
        <!--/.info-box-->
    </div>
</div>
    
<div class="row">

    <!-- bonus reachat -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box brown-bg">
            <i class="fa fa-inbox"></i>
            <div class="count"><?php echo ("{$compte->getSolde()} {$config->get("devise")}"); ?></div>
            <div class="title">Total</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box brown-bg">
            <i class="fa fa-trash-o"></i>
            <div class="count"><?php echo ("{$compte->getWithdrawals()}  {$config->get("devise")}"); ?></div>
            <div class="title">Transh</div>
        </div>
        <!--/.info-box-->
    </div>
</div>

<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
    			<h2 class="panel-title"><?php echo htmlspecialchars($_REQUEST[AccountController::ATT_GRADE_MEMBER]->grade->name); ?></h2>
			</div>
    		<div class="panel-body">
    			<img class="thumbnail" alt="" src="/<?php echo ($_REQUEST[AccountController::ATT_GRADE_MEMBER]->grade->icon); ?>">
    		</div>
    		<div class="panel-footer">
    			<h3 class="text-info">sponsoring: <?php echo ($_REQUEST[AccountController::ATT_GRADE_MEMBER]->grade->percentage); ?>%</h3>
    			<h4 class="text-danger">up to <?php echo ($_REQUEST[AccountController::ATT_GRADE_MEMBER]->grade->maxGeneration->name); ?></h4>
    		</div>
		</div>
	</div>
</div>