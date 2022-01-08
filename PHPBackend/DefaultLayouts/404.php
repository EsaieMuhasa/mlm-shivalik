<?php
use PHPBackend\Text\HtmlFormater;
?>


<div class="alert alert-danger">
    <h1 class="alert-title">HTTP/1.0 404 Not Found!</h1>
    <p>
    	<?php echo HtmlFormater::toHTML($_REQUEST['message']);?>
    </p>
</div>



