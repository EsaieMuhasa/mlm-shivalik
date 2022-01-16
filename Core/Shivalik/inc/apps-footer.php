<?php 
use PHPBackend\Request;

$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<footer class="text-center" style="padding-top: 70px;">
	<p style="border-top: 1px solid #c0c0c0;" class="">
		Designed by <a href="mailto:<?php echo htmlspecialchars($config->get('designerEmail')); ?>" rel="nofollow"><?php echo htmlspecialchars($config->get('designerName')); ?></a>
	</p>
</footer>