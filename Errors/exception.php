<?php
use Library\Text\HtmlFormater;
?>

<div class="alert alert-danger">
    <h1 class="alert-title">HTTP/1.0 <?php echo $_REQUEST['exception']->getCode();?> Internal Server Error!</h1>
   	<div class="alert-body">
        <p>
        	<?php echo HtmlFormater::toHTML($_REQUEST['exception']->getMessage());?>
        </p>
        <small class="block pd-t-10 pd-b-10"><span class="fa fa-attention"></span>For more information check the error log, or contact the webmaster.</small>
   	</div>
    <div class="">
        <a href="/" class="btn btn-care btn-danger">
        	<span class="fa fa-home"></span>Go at home page
        </a>
    </div>
</div>


