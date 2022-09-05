<div>
	<h1>
	<?php echo 'HTTP/1.0 404 Not Found';?>
	</h1>
	<p>
        <?php 
        echo ($_REQUEST['message'].'');
        echo '"code": 404';
        ?>
	</p>
</div>