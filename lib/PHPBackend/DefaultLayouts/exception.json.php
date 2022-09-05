
<?php 
echo '"title":"HTTP/1.0 404 Not Found",';
echo '"message":"'.$_REQUEST['exception']->getMessage().'",';
echo '"code": '.$_REQUEST['exception']->getCode().',';
echo '"exception":'.$_REQUEST['exception']->toJSON();
?>