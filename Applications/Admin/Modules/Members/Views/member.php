<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $compte
 */
$compte = $_REQUEST[MembersController::ATT_COMPTE];

if (isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])) {
	/**
	 * @var GradeMember $gradeMember
	 */
	$gradeMember = $_REQUEST[MembersController::ATT_GRADE_MEMBER];
} else {
	$gradeMember = null;
}

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title"><span class="fa fa-users"></span> Generationnel Point value</strong>
	</div>
	<div class="panel-body">
        
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getLeftMembershipPv()); ?></div>
                    <div class="title">Left PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getMiddleMembershipPv()); ?></div>
                    <div class="title">MIDDLE PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box blue-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getRightMembershipPv()); ?></div>
                    <div class="title">Right PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box brown-bg">
                    <i class="fa fa-tags"></i>
                    <div class="count"><?php echo ($compte->getMembershipPv()); ?></div>
                    <div class="title">TOTAL PV</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title"><span class="glyphicon glyphicon-shopping-cart"></span> Point value of purchase products</strong>
	</div>
	<div class="panel-body">
        
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getLeftProductPv()); ?></div>
                    <div class="title">Left PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getMiddleProductPv()); ?></div>
                    <div class="title">MIDDLE PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="fa fa-tag"></i>
                    <div class="count"><?php echo ($compte->getRightProductPv()); ?></div>
                    <div class="title">Right PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box brown-bg">
                    <i class="glyphicon glyphicon-gift"></i>
                    <div class="count"><?php echo ($compte->getPersonalProductPv()); ?></div>
                    <div class="title">PERSO PV</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box brown-bg">
                    <i class="fa fa-tags"></i>
                    <div class="count"><?php echo ($compte->getProductPv()); ?></div>
                    <div class="title">TOTAL PV</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
	</div>
</div>
    
<?php $hasOffice = $compte->getMember()->getOfficeAccount() != null; ?>
<div class="row">
    <!-- montant disponible -->
    <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="fa fa-users"></i>
            <div class="count"><?php echo ("{$compte->getSoldeGenration()} {$config->get("devise")}"); ?></div>
            <div class="title">Reg <?php echo htmlspecialchars("&"); ?>  Upg Wallet</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <!-- bonus reachat -->
    <div class="<?php echo ($hasOffice? 'col-lg-4 col-md-4':'col-lg-6 col-md-6'); ?> col-sm-6 col-xs-12">
        <div class="info-box dark-bg">
            <i class="glyphicon glyphicon-shopping-cart"></i>
            <div class="count"><?php echo ("{$compte->getPurchaseBunus()} {$config->get("devise")}"); ?></div>
            <div class="title">Purchase bonus</div>
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
        <div class="info-box red-bg">
            <i class="fa fa-trash-o"></i>
            <div class="count"><?php echo ("{$compte->getWithdrawals()}  {$config->get("devise")}"); ?></div>
            <div class="title">Transh</div>
        </div>
        <!--/.info-box-->
    </div>

    <!-- bonus reachat -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="info-box brown-bg">
            <i class="fa fa-money"></i>
            <div class="count"><?php echo ("{$compte->getSolde()} {$config->get("devise")}"); ?></div>
            <div class="title">Sold</div>
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