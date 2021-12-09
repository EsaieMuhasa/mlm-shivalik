<?php
use Library\Page;
use Library\Config;

$config = Config::getInstance();
?>
<!DOCTYPE html>

<html lang="en">
    <head>
    
     	<title>Shivalik</title>
    	<link rel="icon" type="image/jpg" href="<?php echo $config->get('logo'); ?>" />
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content=""/>
        <meta name="keyword" content="">
    	<meta name="viewport" content="width=device-width,height=device-height,user-scalable=1"/>
    
        <!-- Bootstrap CSS -->
        <link href="/css/bootstrap-index.css" rel="stylesheet">
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <!-- bootstrap theme -->
        <link href="/css/bootstrap-theme.css" rel="stylesheet">
        <!--external css-->
        <!-- font icon -->
        <link href="/css/elegant-icons-style.css" rel="stylesheet" />
        <link href="/css/font-awesome.min.css" rel="stylesheet" />
        <!-- Custom styles -->
        <link href="/css/style.css" rel="stylesheet">
        <link href="/css/style-responsive.css" rel="stylesheet" />
    
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 -->
        <!--[if lt IE 9]>
          <script src="/js/html5shiv.js"></script>
          <script src="/js/respond.min.js"></script>
          <script src="/js/lte-ie7.js"></script>
        <![endif]-->
    
    </head>
    
    <body>
        <!-- container section start -->
        <section id="container" class="">
            <!--header start-->
            <?php require 'apps-header.php';?>
            <!--sidebar start-->
            <?php require 'apps-navigation.php';?>
            <!--sidebar end-->
            <!--main content start-->
            <section id="main-content">
                <section class="wrapper">
                    <?php require 'apps-msg-session.php';?>
                    <?php echo $_REQUEST[Page::ATT_VIEW];?>
                </section>
            </section>
            <!--main content end-->
            <?php require 'apps-footer.php';?>
        </section>
        <!-- container section end -->
        <!-- javascripts -->
        <script src="/js/jquery.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <!-- nice scroll -->
        <script src="/js/jquery.scrollTo.min.js"></script>
        <script src="/js/jquery.nicescroll.js" type="text/javascript"></script>
        <!--custome script for all page-->
        <script src="/js/scripts.js"></script>
        <script type="text/javascript">
            $(function (){
            	$('#modal-session-message').modal('show');
            });
        </script>
    </body>
</html>
