<?php
use Applications\Admin\Modules\Budget\BudgetController;
use PHPBackend\Page;
?>

<h3 class="page-header">
	<i class="fa fa-cogs"></i> <?php echo ($_REQUEST[BudgetController::ATT_VIEW_TITLE]); ?>
</h3>
<hr/>
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
			<a class="navbar-brand" href="/admin/budget/">
                <i class="fa fa-cogs"></i> <?php echo ($_REQUEST[BudgetController::ATT_VIEW_TITLE]); ?>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo ($_REQUEST['config_nav'] == 'home'? 'active' : ''); ?>">
	        		<a href="/admin/budget/">
	        			<span class="glyphicon glyphicon-dashboard"></span> Home
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo ($_REQUEST['config_nav'] == 'element'? 'active' : ''); ?>">
	        		<a href="/admin/budget/element-config" title="">
            			<span class="fa fa-list"></span> Config elements
            		</a>
	        	</li> 
			</ul>
			
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<?php echo $_REQUEST[Page::ATT_VIEW]; ?>