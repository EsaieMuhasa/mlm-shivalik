<?php 
echo "<title>HTTP/1.0 {$_REQUEST['exception']->getCode()} Internal Server Error!</title>";
echo "<message>{$_REQUEST['exception']->getMessage()}</message>";
echo $_REQUEST['exception']->toXML();
?>
