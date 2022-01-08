<?php
use Applications\Admin\Modules\Members\MembersController;
use Library\Config;

/**
 * @var \Entities\Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var \Entities\Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];

if (isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])) {
	/**
	 * @var \Entities\GradeMember $gradeMember
	 */
	$gradeMember = $_REQUEST[MembersController::ATT_GRADE_MEMBER];
} else {
	$gradeMember = null;
}

$config = Config::getInstance();
?>

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
            <div class="title">ALL PV</div>
        </div>
        <!--/.info-box-->
    </div>
</div>
<div class="row">
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
        <div class="info-box red-bg">
            <i class="fa fa-trash-o"></i>
            <div class="count"><?php echo ("{$compte->getWithdrawals()} {$config->get("devise")}"); ?></div>
            <div class="title">Transh</div>
        </div>
        <!--/.info-box-->
    </div>
</div>

<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<?php echo htmlspecialchars($compte->getMember()->getPacket()->getGrade()->getName()); ?>
			</div>
			<div class="panel-body">
				<img alt="" class="thumbnail" src="/<?php echo ($compte->getMember()->getPacket()->getGrade()->getIcon()); ?>">
			</div>
			<div class="panel-footer">
				up to <span class="badge"><?php echo htmlspecialchars($compte->getMember()->getPacket()->getGrade()->getMaxGeneration()->getNumber()); ?> th</span> generation
			</div>
		</div>
	</div>
</div>