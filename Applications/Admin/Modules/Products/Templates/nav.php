<?php
use Applications\Admin\Modules\Products\ProductsController;
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
			<a class="navbar-brand" href="/admin/products/dashborad.html">
				<span class="fa fa-leaf"></span>
			</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_DASHBOARD)? "active" : ""; ?>">
	        		<a href="/admin/products/dashboard.html">
	        			<span class="glyphicon glyphicon-dashboard"></span> Dashboard
	        		</a>
	        	</li>
	        	
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_PRODUCTS)? "active" : ""; ?>">
	        		<a href="/admin/products/">
            			<span class="fa fa-leaf"></span> Products
            		</a>
	        	</li>	        	
	        	
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_STOCKS)? "active" : ""; ?>">
	        		<a href="/admin/stocks/">
            			<span class="fa fa-database"></span> Stocks
            		</a>
	        	</li>
			</ul>
			
			<ul class="nav navbar-nav pull-right">
	        	<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_OTHER_OPTERATIONS)? "active" : ""; ?>" >
					<a href="/admin/products/add.html"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                		<span class="fa fa-plus"></span> other operations
                	</a>
                	<ul class="dropdown-menu">
                		<li class="<?php echo (isset($_REQUEST[ProductsController::ATT_ACTIVE_MENU]) && $_REQUEST[ProductsController::ATT_ACTIVE_MENU] == ProductsController::ITEM_MENU_ADD_PRODUCT)? "active" : ""; ?>">
        	        		<a href="/admin/products/add.html">
                    			<span class="fa fa-plus"></span> add new products
                    		</a>
        	        	</li>	        	
        	        	
        	        	<li class="<?php echo ""; ?>">
        	        		<a href="/admin/stocks/add.html">
                    			<span class="fa fa-plus"></span> add new stocks
                    		</a>
        	        	</li>
        	        	<li role="separator" class="divider"></li>
        	        	<li class="">
        	        		<a href="/admin/stocks/add.html">
                    			<span class="fa fa-time"></span> Time line
                    		</a>
        	        	</li>
                	</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>