<?php
use Applications\Member\Modules\Account\AccountController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Filters\SessionMemberFilter;
use PHPBackend\AppConfig;
use PHPBackend\Request;
/**
 * @var Member $member
 */
$member = $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION];

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
</div>
    
<?php $hasOffice = $compte->getMember()->getOfficeAccount() != null; ?>
<div class="row">
    <!-- montant disponible -->
    <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-money"></i>
            <div class="count"><?php echo ("{$compte->getSolde()} {$config->get("devise")}"); ?></div>
            <div class="title">Reg <?php echo htmlspecialchars("&"); ?>  Upg Wallet</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-link"></i>
            <div class="count">0<?php echo ("{$config->get("devise")}"); ?></div>
            <div class="title">Reachat</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <?php if ($hasOffice) : ?>
    <!-- bonus reachat -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="fa fa-laptop"></i>
            <div class="count"><?php echo ("{$compte->getSoldeOfficeBonus()} {$config->get("devise")}"); ?></div>
            <div class="title">Office</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endif; ?>
</div>
    
<div class="row">

    <!-- bonus reachat -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box brown-bg">
            <i class="fa fa-inbox"></i>
            <div class="count"><?php echo ("{$compte->getSolde()} {$config->get("devise")}"); ?></div>
            <div class="title">Total</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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