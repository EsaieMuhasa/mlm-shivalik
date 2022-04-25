<?php
use Applications\Office\Modules\Products\ProductsController;
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-leaf"></i> <?php echo ($_REQUEST[ProductsController::ATT_VIEW_TITLE]); ?>
    		<span class="badge"><?php echo ($_REQUEST[ProductsController::ATT_COUNT_PRODUCT]); ?></span>
    	</h3>
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
			<a class="navbar-brand" href="/office/products/dashborad.html">
				<span class="fa fa-leaf"></span>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_DASHBOARD)? "active" : ""; ?>">
	        		<a href="/office/products/">
	        			<span class="glyphicon glyphicon-dashboard"></span> Dashboard
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_PRODUCTS)? "active" : ""; ?>">
	        		<a href="/office/products/vailable/">
            			<span class="fa fa-leaf"></span> Products
            		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_COMMAND)? "active" : ""; ?>">
	        		<a href="/office/products/command/">
            			<span class="icon_folder-add_alt"></span> Command
            		</a>
	        	</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>