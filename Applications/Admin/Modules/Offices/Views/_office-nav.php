<?php
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;

/**
 * @var Office $dashboard
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-briefcase"></i> <?php echo ($_REQUEST[OfficesController::ATT_VIEW_TITLE]); ?></h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-briefcase"></i>
    			<a href="/admin/offices/">Offices</a>
			</li>
			<li>
    			<i class="fa fa-laptop"></i><?php echo htmlspecialchars($office->name); ?>
			</li>
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
			<a class="navbar-brand" href="/admin/offices/<?php echo $office->id; ?>/">
				<i class="fa fa-laptop"></i><?php echo htmlspecialchars($office->name); ?>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_DASHBOARD)? "active":""; ?>">
	        		<a href="/admin/offices/<?php echo $office->id; ?>/">
	        			<span class="glyphicon glyphicon-dashboard"></span> Dashboard
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_MEMBERS)? "active":""; ?>">
	        		<a href="/admin/offices/<?php echo $office->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		Members <span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
		            	<li><a href="#">Membership</a></li>
		            	<li><a href="#">Upgrades accounts</a></li>
		            	<li role="separator" class="divider"></li>
		            	<li><a href="#">Show all</a></li>
		          	</ul>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_WITHDRAWALS)? "active":""; ?>">
		         	<a href="/admin/offices/<?php echo $office->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		         		Withdrawals <span class="caret"></span>
		         	</a>
		          	<ul class="dropdown-menu">
		            	<li><a href="#">Requested</a></li>
		            	<li><a href="#">Served</a></li>
		            	<li role="separator" class="divider"></li>
		            	<li><a href="#">Show all</a></li>
					</ul>
				</li>
				
				<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_OFFICE_ADMIN)? "active":""; ?>">
					<a href="/admin/offices/<?php echo $office->id; ?>/admin.html">
						Office Admin
					</a>
				</li>
				
				<li class="<?php echo (isset($_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU]) && $_REQUEST[OfficesController::ATT_ACTIVE_ITEM_MENU] == OfficesController::ATT_ITEM_MENU_VIRTUAL_MONEY)? "active":""; ?>">
					<a href="/admin/offices/<?php echo $office->id; ?>/virtualmoney/">
						Virtual money
					</a>
				</li>
			</ul>
	
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="/admin/offices/<?php echo $office->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<span class="glyphicon glyphicon-calendar"></span> History <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
		            	<li><a href="/admin/offices/<?php echo $office->id; ?>/history/members/">Members</a></li>
		            	<li><a href="/admin/offices/<?php echo $office->id; ?>/history/withdrawals/">Withdrawals</a></li>
		            	<li><a href="/admin/offices/<?php echo $office->id; ?>/history/products/">Products</a></li>
		            	<li role="separator" class="divider"></li>
		            	<li><a href="/admin/offices/<?php echo $office->id; ?>/history/">Shows all</a></li>
	          		</ul>
	        	</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
