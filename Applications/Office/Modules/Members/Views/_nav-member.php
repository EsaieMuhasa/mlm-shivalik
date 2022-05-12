<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\MonthlyOrder;
use Applications\Office\Modules\Members\MembersController;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $account
 */
$account = $_REQUEST[MembersController::ATT_COMPTE];


/**
 * @var GradeMember $gradeMember
 * @var GradeMember $requestedGradeMember
 */
if (isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])) {
	$gradeMember = isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])? $_REQUEST[MembersController::ATT_GRADE_MEMBER] : null;
}else {
	$gradeMember = null;
}
$requestedGradeMember = isset($_REQUEST[MembersController::ATT_REQUESTED_GRADE_MEMBER])? $_REQUEST[MembersController::ATT_REQUESTED_GRADE_MEMBER]:null;

//$config = Config::getInstance();

$option = isset($_GET['option'])? $_GET['option'] : null;
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-users"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-users"></i>
    			<a href="/office/members/">Members</a>
			</li>
			<?php if ($option==null) { ?>
    		<li>
    			<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo ("{$member->getPhoto()}") ?>">
    			<?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}"); ?>
			</li>
    		<?php } else {?>
    		<li>
    			<a class="" href="/office/members/<?php echo $member->getId().'/'; ?>" title="dashbord of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
    				<i class="fa fa-user"></i><?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}") ?>
    			</a>
			</li>
    		<?php } ?>

    		<?php if ($option!=null) { ?>
    			<li>
        			<?php if (isset($_GET['foot'])){ ?>
        			<a href="/office/members/<?php echo "{$member->getId()}/{$option}/"; ?>">
        				<span class="fa fa-sitemap"></span>
        				<?php echo htmlspecialchars("{$option}") ?>
    				</a>
        			<?php } else { ?>
        			<?php echo htmlspecialchars("{$option}") ?>
        		<?php } ?>
    			</li>
    		<?php }?>
    		
    		<?php if (isset($_GET['foot'])){ ?>
    		<li><i class="fa fa-tag"></i><?php echo (($_GET['foot'] == "all")? ("ALL"):(strtoupper($_GET['foot']))); ?></li>
    		<?php }?>
    	</ol>
    </div>
</div>


<div class="row">
	<?php if ($gradeMember!=null) { ?>
	<div class="col-sm-2 col-xs-6">
		<div class="thumbnail text-left text-center">
			<?php if ($requestedGradeMember!=null) : ?>
			<span class="label label-info" style="display: block;">current</span>
			<?php endif; ?>
			<img style="" alt="" src="/<?php echo ("{$gradeMember->getGrade()->getIcon()}") ?>">
			<?php echo htmlspecialchars("{$gradeMember->getGrade()->getName()}") ?>
		</div>
	</div>
	<?php } ?>
	
	<?php if ($requestedGradeMember!=null) { ?>
	<div class="col-sm-2 col-xs-6">
		<span class="thumbnail text-left">
			<span class="label label-danger" style="display: block;">requested</span>
			<img style="" alt="" src="/<?php echo ("{$requestedGradeMember->getGrade()->getIcon()}") ?>">
			<?php echo htmlspecialchars("{$requestedGradeMember->getGrade()->getName()}") ?>
		</span>
	</div>
	<?php } ?>
	
    <?php if (isset($_REQUEST[MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT])) : ?>
    <?php 
    /**
     * @var MonthlyOrder $monthly
     */
    $monthly = $_REQUEST[MembersController::ATT_MONTHLY_ORDER_FOR_ACCOUNT]; ?>
	<div class="<?php echo (($requestedGradeMember!=null)? 'col-sm-8':'col-sm-10'); ?> col-xs-12">
		<div class="alert alert-info">
			<strong><span class="glyphicon glyphicon-warning-sign"></span> Purchase accounting for the month of <?php echo $monthly->getFormatedDateAjout("M Y") ?> </strong>
			<table class="table table-bordered table-condansed">
				<tbody>
					<tr>
						<td>Amount realize </td>
						<td><?php echo $monthly->getAmount(); ?> USD</td>
					</tr>
					<tr>
						<td>Used amount</td>
						<td><?php echo $monthly->getUsed(); ?> USD</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th>Available amount</th>
						<th><?php echo $monthly->getAvailable(); ?> USD</th>
					</tr>
				</tfoot>
			</table>
			
			<a class="btn btn-danger" href="affiliate.html">
				<span class="fa fa-user"></span> Affiliate a new member
			</a>
		</div>
		
	</div>
    <?php endif; ?>
	
</div>
<hr/>

<div class="row">
	<div class="col-xs-12">
	
		<!-- 
		<a class="btn btn-primary" href="/office/members/<?php echo $member->getId().'/'.($member->isEnable()? 'disable':'enable'); ?>.html" title="change state of <?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}") ?>">
			<?php echo ($member->isEnable()? 'Disable':'Enable'); ?> account
		</a>
		 -->
		 
		<a class="btn btn-primary" href="/office/members/<?php echo $member->getId().'/'; ?>withdrawals.html" title="show withdrawals of account <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-money"></span> Withdrawals
		</a>
		
		<?php if ($option != 'downlines') { ?>
		<a class="btn btn-primary" href="/office/members/<?php echo $member->getId().'/'; ?>downlines/" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-sitemap"></span> Downlines
		</a>
		<?php } ?>
		
		<?php if ($member->isEnable() && !$option == 'upgrade' && $requestedGradeMember==null && ($gradeMember!=null && $gradeMember->getGrade()->getMaxGeneration()->getNumber() < Generation::MAX_GENERATION)) { ?>
		<a class="btn btn-success" href="/office/members/<?php echo $member->getId().'/'; ?>upgrade.html" title="upgrade account rang of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-sort-up"></span> Upgrade
		</a>
		<?php } ?>
	</div>
</div>
<hr/>