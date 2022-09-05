<?php
use Applications\Office\Modules\Dashboard\DashboardController;
?>


<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-send"></i> <?php echo ($_REQUEST[DashboardController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
</div>
<hr/>

<section class="panel">
    <header class="panel-heading">
    	Virtual money request form
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
		</form>
	</div>
</section>