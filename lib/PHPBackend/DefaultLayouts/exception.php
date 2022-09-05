<?php
use PHPBackend\Text\HtmlFormater;
?>


<div class="panel panel-danger">
	<div class="panel-heading panel-header">
    	<h1 class="panel-title text-danger">HTTP/1.0 <?php echo $_REQUEST['exception']->getCode();?> Internal Server Error!</h1>
	</div>
   	<div class="panel-body">
        <p>
        	<?php echo HtmlFormater::toHTML($_REQUEST['exception']->toHTML());?>
        </p>
   	</div>
   	<div class="panel-footer">
   		PHPBackend
   	</div>
</div>


