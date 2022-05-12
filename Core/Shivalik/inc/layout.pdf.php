<?php
use PHPBackend\Page;
use PHPBackend\Request;
use PHPBackend\AppConfig;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>Shivalik Herbals</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/jpg" href="/img/logo-32x32.png" />
        
        <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
        <?php echo file_get_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'style.css'); ?>
        </style>
    </head>
    
    <body>
        <!-- .container-fluid --> 
        <div class="container-fluid">
            <?php echo $_REQUEST[Page::ATT_VIEW]; ?>
        </div>
        <!-- /.container-fluid --> 
    </body>
</html>