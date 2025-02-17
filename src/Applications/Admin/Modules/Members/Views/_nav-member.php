<?php
use Applications\Admin\Modules\Members\MembersController;
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Entities\Account;
use Core\Shivalik\Entities\Generation;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Member;

/**
 * @var Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var Account $account
 */
$account = isset($_REQUEST[MembersController::ATT_COMPTE])? $_REQUEST[MembersController::ATT_COMPTE] : null;


/**
 * @var GradeMember $gradeMember
 * @var GradeMember $requestedGradeMember
 */
$gradeMember = isset($_REQUEST[MembersController::ATT_GRADE_MEMBER])? $_REQUEST[MembersController::ATT_GRADE_MEMBER] : null;
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
    			<a href="/admin/members/">Members <span class="badge"><?php echo ($_REQUEST[MembersController::PARAM_MEMBER_COUNT]); ?> </span></a>
			</li>
			<?php if ($option==null) { ?>
    		<li>
    			<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo ("{$member->getPhoto()}") ?>">
    			<?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}"); ?>
			</li>
    		<?php } else {?>
    		<li>
    			<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>" title="dashbord of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
    				<i class="fa fa-user"></i><?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}") ?>
    			</a>
			</li>
    		<?php } ?>

    		<?php if ($option!=null) { ?>
    			<li>
        			<?php if (isset($_GET['foot'])){ ?>
        			<a href="/admin/members/<?php echo "{$member->getId()}/{$option}/"; ?>">
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
	<div class="col-sm-2 col-xs-4">
		<div class="thumbnail text-left">
			<img style="" alt="" src="/<?php echo ("{$gradeMember->getGrade()->getIcon()}") ?>">
			<?php echo htmlspecialchars("{$gradeMember->getGrade()->getName()}") ?>
		</div>
	</div>
	<?php } ?>
	
	<?php if ($requestedGradeMember!=null) { ?>
	<div class="col-sm-2 col-xs-6">
		<a class="thumbnail text-left" title="click to activate the packages" href="/admin/members/<?php echo "{$member->getId()}/certify-{$requestedGradeMember->getId()}"; ?>.html">
			<img style="" alt="" src="/<?php echo ("{$requestedGradeMember->getGrade()->getIcon()}") ?>">
			<?php echo htmlspecialchars("{$requestedGradeMember->getGrade()->getName()}") ?>
		</a>
	</div>
	<?php } ?>
</div>
<hr/>

<div class="row">
	<div class="col-xs-12">
	
		<!-- 
		<a class="btn btn-primary" href="/admin/members/<?php echo $member->getId().'/'.($member->isEnable()? 'disable':'enable'); ?>.html" title="change state of <?php echo htmlspecialchars("{$member->getLastName()} {$member->getName()}") ?>">
			<?php echo ($member->isEnable()? 'Disable':'Enable'); ?> account
		</a>
		 -->
		 
		<a class="btn btn-primary" href="/admin/members/<?php echo $member->getId().'/'; ?>withdrawals.html" title="show withdrawals of account <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-money"></span> Withdrawals
		</a>
		
		<?php if ($option != 'downlines') { ?>
		<a class="btn btn-primary" href="/admin/members/<?php echo $member->getId().'/'; ?>downlines/" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-sitemap"></span> Downlines
		</a>
		<?php } ?>
		
		<?php if ($member->isEnable() && !$option == 'upgrade' && $requestedGradeMember==null && ($gradeMember!=null && $gradeMember->getGrade()->getMaxGeneration()->getNumber() < Generation::MAX_GENERATION)) { ?>
		<a class="btn btn-success" href="/admin/members/<?php echo $member->getId().'/'; ?>upgrade.html" title="upgrade account rang of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-sort-up"></span> Upgrade
		</a>
		<?php } ?>
		
		<?php if ($option != 'update') { ?>
		<a class="btn btn-info" href="/admin/members/<?php echo $member->getId().'/'; ?>update.html" title="update profil of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-edit"></span> Update
		</a>
		<?php }?>
		
		<?php if ($option != 'password') { ?>
		<a class="btn btn-danger" href="/admin/members/<?php echo $member->getId().'/'; ?>password.html" title="update password of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
			<span class="fa fa-key"></span> Reset password
		</a>
		<?php }?>
	</div>
</div>
<hr/>