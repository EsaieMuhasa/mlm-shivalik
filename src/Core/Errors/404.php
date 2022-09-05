<?php
use PHPBackend\Text\HtmlFormater;
?>

<div class="alert alert-danger">
    <h1 class="alert-title">HTTP/1.0 404 Not Found!</h1>
    <p>
    	<?php echo HtmlFormater::toHTML($_REQUEST['errorMessage']);?>
    </p>
    
    <div class="">
        <a href="/" class="btn btn-danger">
        	<span class="fa fa-home"></span>Aller à la page d'accueil
        </a>
    </div>
</div>



