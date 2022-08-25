<?php
use Core\Shivalik\Entities\Account;
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
        				<?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $option))) ?>
    				</a>
        			<?php } else { ?>
        			<?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $option))) ?>
        		<?php } ?>
    			</li>
    		<?php }?>
    		
    		<?php if (isset($_GET['foot'])){ ?>
    		<li><i class="fa fa-tag"></i><?php echo (($_GET['foot'] == "all")? ("ALL"):(strtoupper($_GET['foot']))); ?></li>
    		<?php }?>
    	</ol>
    </div>
</div>

<nav class="navbar navbar-default">
	<div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        	<span class="sr-only">Toggle navigation</span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo "/office/members/{$member->id}/";?>">
				<span class="fa fa-user"></span> <?php echo htmlspecialchars("{$member->lastName}");?>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo $option == null? 'active' : ''; ?>">
	        		<a href="<?php echo "/office/members/{$member->id}/";?>">
	        			<span class="glyphicon glyphicon-dashboard"></span> Profil
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo $option == 'withdrawals'? 'active' : ''; ?>">
					<a class="" href="/office/members/<?php echo $member->getId().'/'; ?>withdrawals.html" title="show withdrawals of account <?php echo htmlspecialchars("{$member->getNames()}") ?>">
						<span class="fa fa-money"></span> Withdrawals
					</a>
	        	</li>
	        	
	        	<li class="<?php echo $option == 'sell-sheet'? 'active' : ''; ?>">
					<a class="" href="/office/members/<?php echo $member->getId().'/'; ?>sell-sheet/" title="show sell sheet of account <?php echo htmlspecialchars("{$member->getNames()}") ?>">
						<span class="fa fa-book"></span> Sell Sheet
					</a>
	        	</li>
				
	        	<li class="<?php echo $option == 'downlines'? 'active' : ''; ?>">
					<a class="" href="/office/members/<?php echo $member->getId().'/'; ?>downlines/" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
						<span class="fa fa-sitemap"></span> Downlines
					</a>
	        	</li>
	        	
	        	<li class="<?php echo $option == 'upgrade'? 'active' : ''; ?>">
					<a class="" href="/office/members/<?php echo $member->getId().'/'; ?>upgrade.html" title="upgrade account rang of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
						Upgrade
					</a>
	        	</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>

<div class="row">	
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