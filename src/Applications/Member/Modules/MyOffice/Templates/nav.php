<?php
use Core\Shivalik\Filters\SessionMemberFilter;
use Applications\Member\Modules\MyOffice\MyOfficeController;

/**
 * @var \Core\Shivalik\Entities\Office $dashboard
 */
$office = $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION]->getOfficeAccount();

?>

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
			<a class="navbar-brand" href="/member/office/">
				<i class="fa fa-laptop"></i><?php echo htmlspecialchars($office->name); ?>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_DASHBOARD)? "active":""; ?>">
	        		<a href="/member/office/">
	        			<span class="glyphicon glyphicon-dashboard"></span> Dashboard
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_MEMBERS)? "active":""; ?>">
	        		<a href="/member/office/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		<span class="fa fa-users"></span> Users<span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
		            	<li><a href="/member/office/members/"><span class="fa fa-users"></span> Membership</a></li>
		            	<li><a href="/member/office/upgrades.html"><span class="glyphicon glyphicon-refresh"></span>Upgrades accounts</a></li>
		            	<li role="separator" class="divider"></li>
        				<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_OFFICE_ADMIN)? "active":""; ?>">
        					<a href="/member/office/admin.html">
        						<span class="glyphicon glyphicon-user"></span> Office Admin
        					</a>
        				</li>
		          	</ul>
	        	</li>
				
				<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_VIRTUAL_MONEY)? "active":""; ?>">
					<a href="/member/office/virtualmoney/">
						Virtual money
					</a>
				</li>
				
				<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_WITHDRAWALS)? "active":""; ?>">
					<a href="/member/office/withdrawals/">
						Cash outs
					</a>
				</li>
			</ul>
				
			<ul class="nav navbar-nav navbar-right">
				<li class="<?php echo (isset($_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[MyOfficeController::ATT_ACTIVE_ITEM_MENU] == MyOfficeController::ATT_ITEM_MENU_HISTORY)? "active":""; ?>">
					<a href="/member/office/history/" class="">
						<span class="fa fa-calendar"></span> History
					</a>
	        	</li>
			</ul>
			
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
