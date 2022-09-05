<?php 
use PHPBackend\Request;

$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<footer class="text-center">
	<p style="border-top: 1px solid #c0c0c0;padding-top: 15px;" class="">
		Designed by <a href="mailto:<?php echo htmlspecialchars($config->get('designerEmail')); ?>" rel="nofollow"><?php echo htmlspecialchars($config->get('designerName')); ?></a>
	</p>
</footer>