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

<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title"><span class="fa fa-tags"></span> Point value</strong>
	</div>
	<div class="panel-body">
        
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo empty($member->getLeftMembershipPv()) ? '0' : ($member->getLeftMembershipPv()); ?></div>
                    <div class="title">Left PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo empty($member->getMiddleMembershipPv())? '0' : $member->getMiddleMembershipPv(); ?></div>
                    <div class="title">MIDDLE PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo empty($member->getRightMembershipPv())? '0' : $member->getRightMembershipPv(); ?></div>
                    <div class="title">Right PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
        </div>
        
        <div class="row">
        	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($member->getProductPv()); ?></div>
                    <div class="title">Purchase PV</div>
                </div>
                <!--/.info-box-->
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="fa fa-tags"></i>
                    <div class="count"><?php echo ($member->getMembershipPv() + $member->getProductPv()); ?></div>
                    <div class="title">TOTAL PV</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title"><span class="fa fa-money"></span> Wallet</strong>
	</div>
	<div class="panel-body">
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="fa fa-money"></i>
                    <div class="count"><?php echo ("{$member->getAvailableCashMoney(true)} {$config->get("devise")}"); ?></div>
                    <div class="title">Sold</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="fa fa-trash-o"></i>
                    <div class="count"><?php echo ("{$member->getWithdrawals()}  {$config->get("devise")}"); ?></div>
                    <div class="title">Transh</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
        
        <?php $hasOffice = $compte->getMember()->getOfficeAccount() != null; ?>
        <div class="row">
            <!-- montant disponible -->
            <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-users"></i>
                    <div class="count"><?php echo ("{$member->getSoldGeneration()} {$config->get("devise")}"); ?></div>
                    <div class="title">generationnel bonus</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <!-- bonus reachat -->
            <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="glyphicon glyphicon-shopping-cart"></i>
                    <div class="count"><?php echo ("{$member->getPurchaseBonus()} {$config->get("devise")}"); ?></div>
                    <div class="title">Purchase bonus</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <?php if ($hasOffice) : ?>
            <!-- bonus reachat -->
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-laptop"></i>
                    <div class="count"><?php echo ("{$member->getSoldOfficeBonus()} {$config->get("devise")}"); ?></div>
                    <div class="title">Office bonus</div>
                </div>
                <!--/.info-box-->
            </div>
            <?php endif; ?>

            <!-- mobilisator -->
            <?php if ($member->hasParticularOperation()) : ?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-leaf"></i>
                    <div class="count"><?php echo ("{$member->getParticularBonus()} {$config->get("devise")}"); ?></div>
                    <div class="title">Mobilisator</div>
                </div>
            </div>
            <?php endif; ?>
            <!-- // -->
        </div>
	    
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