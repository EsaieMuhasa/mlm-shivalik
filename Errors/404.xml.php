<?php 
echo '<title>HTTP/1.0 404 Not Found</title>';
echo "<message>{$_REQUEST['errorMessage']}</message>";
echo "<reel-code>{$_REQUEST['errorCode']}</reel-code>";
?>