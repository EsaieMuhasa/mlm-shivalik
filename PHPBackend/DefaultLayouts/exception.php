<?php
use PHPBackend\Text\HtmlFormater;
?>


<div class="panel">
    <h1 class="panel-title">HTTP/1.0 <?php echo $_REQUEST['exception']->getCode();?> Internal Server Error!</h1>
   	<div class="panel-body">
        <p>
        	<?php echo HtmlFormater::toHTML($_REQUEST['exception']->toHTML());?>
        </p>
   	</div>
   	<div class="panel-footer">
   		PHPBackend
   	</div>
</div>


