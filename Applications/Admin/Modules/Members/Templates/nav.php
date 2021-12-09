<?php
use Applications\Root\Modules\Settings\SettingsController;
use Applications\Admin\Modules\Members\MembersController;
use Applications\Admin\Modules\Offices\OfficesController;

/**
 * @var \Entities\Member $member
 */
$member = $_REQUEST[MembersController::ATT_MEMBER];

/**
 * @var \Entities\Account $account
 */
$account = isset($_REQUEST[MembersController::ATT_COMPTE])? $_REQUEST[MembersController::ATT_COMPTE] : null;


/**
 * @var \Entities\GradeMember $gradeMember
 * @var \Entities\GradeMember $requestedGradeMember
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

    		<?php if ($option!=null) : ?>
    			<li>
        			<?php if (isset($_GET['foot'])){ ?>
        			<a href="/admin/members/<?php echo "{$member->getId()}/{$option}/"; ?>">
        				<span class="fa fa-sitemap"></span><?php echo htmlspecialchars("{$option}") ?>
    				</a>
        			<?php } else { ?>
        			<i class="fa fa-sitemap"></i><?php echo htmlspecialchars("{$option}") ?>
        		<?php } ?>
    			</li>
    		<?php endif; ?>
    		
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
			<a class="navbar-brand" href="/admin/members/<?php echo $member->id; ?>/">
				<span><img alt="" src="/<?php echo $member->getPhoto(); ?>" style="max-width: 25px;border-radius: 50%;"></span> <?php echo htmlspecialchars($member->getLastName()); ?>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo (isset($_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU]) && $_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU] == MembersController::ATT_ITEM_MENU_DASHBORAD)? "active":""; ?>">
	        		<a href="/admin/members/<?php echo $member->id; ?>/">
	        			<span class="glyphicon glyphicon-dashboard"></span> Dashboard
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU]) && $_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU] == MembersController::ATT_ITEM_MENU_WITHDRAWALS)? "active":""; ?>">
	        		<a href="/admin/members/<?php echo $member->getId().'/'; ?>withdrawals.html" title="show withdrawals of account <?php echo htmlspecialchars("{$member->getNames()}") ?>">
            			<span class="fa fa-money"></span> Withdrawals
            		</a>
	        	</li>
	        	
	        	
	        	<li class="<?php echo (isset($_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU]) && $_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU] == MembersController::ATT_ITEM_MENU_DOWNLINES)? "active":""; ?>">
	        		<a href="/admin/members/<?php echo $member->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		<span class="fa fa-users"></span> Hierarchy<span class="caret"></span>
		         	</a>
		         	
		          	<ul class="dropdown-menu">
        	        	<li class="<?php echo (isset($_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU]) && $_REQUEST[MembersController::ATT_SELECTED_ITEM_MENU] == MembersController::ATT_ITEM_MENU_DOWNLINES)? "active":""; ?>">
            	        	<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>downlines/" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="fa fa-sitemap"></span> Downlines
                    		</a>
        	        	</li>
        	        	<li class="">
            	        	<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>downlines-hierarchy/all.html" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="glyphicon glyphicon-align-center"></span> Downlines hierarchy
                    		</a>
        	        	</li>
        	        	
        	        	<li class="">
            	        	<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>downlines/" title="show downline member's of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="glyphicon glyphicon-tree-conifer"></span> Tree
                    		</a>
        	        	</li>
		            	<li role="separator" class="divider"></li>
        				<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_OFFICE_ADMIN)? "active":""; ?>">
        					<a href="/admin/members/<?php echo $member->id; ?>/admin.html">
        						<span class="glyphicon glyphicon-user"></span> Sponsorship
        					</a>
        				</li>
        				
        				<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_OFFICE_ADMIN)? "active":""; ?>">
        					<a href="/admin/members/<?php echo $member->id; ?>/admin.html">
        						<span class="glyphicon glyphicon-hand-up"></span> Upline
        					</a>
        				</li>
		          	</ul>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_MEMBERS)? "active":""; ?>">
	        		<a href="/admin/members/<?php echo $member->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		<span class="fa fa-list"></span> other option<span class="caret"></span>
		         	</a>
		         	
		          	<ul class="dropdown-menu">
		            	<li>
		            		<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>update.html" title="update profil of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="glyphicon glyphicon-pencil"></span> Update profil
                    		</a>
		            	</li>
		            	<li>
		            		<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>password.html" title="update password of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="fa fa-key"></span> Reset password
                    		</a>
		            	</li>
        	        	<li class="">
            	        	<a class="" href="/admin/members/<?php echo $member->getId().'/'; ?>upgrade.html" title="upgrade account rang of <?php echo htmlspecialchars("{$member->getNames()}") ?>">
                    			<span class="glyphicon glyphicon-export"></span> Upgrade
                    		</a>
        	        	</li>
		            	<li role="separator" class="divider"></li>
        				<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_OFFICE_ADMIN)? "active":""; ?>">
        					<a href="/admin/members/<?php echo $member->id; ?>/admin.html">
        						<span class="glyphicon glyphicon-off"></span> Enable account
        					</a>
        				</li>
		          	</ul>
	        	</li>
	        	
	        	
	        	<li class="">
	        	</li>
				
			</ul>
			
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
