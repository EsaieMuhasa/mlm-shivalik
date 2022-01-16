<?php
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
use PHPBackend\AppConfig;
use PHPBackend\Page;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>Shivalik Herbals</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css"  href="/css/bootstrap.css">
        <link rel="stylesheet" href="/css/style.css"/>
        <link rel="icon" type="image/jpg" href="<?php echo $config->get('logo'); ?>" />
        
        <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    	<link rel="apple-touch-icon" href="<?php echo $config->get('logo'); ?>">
    	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $config->get('logo'); ?>">
    	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $config->get('logo'); ?>">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body>
        <nav id="menu" class="navbar navbar-default navbar-fixed-top">
            <div class="container"> 
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> 
                    	<span class="sr-only">Toggle navigation</span>
                    	<span class="icon-bar"></span>
                    	<span class="icon-bar"></span>
                    	<span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand page-scroll" href="/"><i class="fa fa-diamond"></i> Shivalik</a>
                </div>
                    
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/#page-top" class="page-scroll">Home</a></li>
                        <li><a href="/#about" class="page-scroll">About</a></li>
                        <li><a href="/#portfolio" class="page-scroll">Products</a></li>
                        <li><a href="/#contact" class="page-scroll">Contact</a></li>
                        <li>
                        	<?php if (AdminApplication::getConnectedUser() != null) { ?>
                        	<a href="/admin/" title="<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getNames()); ?>">
                        		<span class="fa fa-user"></span>
                        		<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getLastName()); ?>
                    		</a>
                    		<?php } else if (OfficeApplication::getConnectedUser() != null) { ?>
                        	<a href="/office/" title="<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getNames()); ?>">
                        		<span class="fa fa-user"></span>
                        		<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getLastName()); ?>
                    		</a>
                        	<?php } else if (MemberApplication::getConnectedMember() != null) { ?>
                        	<a href="/member/" title="<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getNames()); ?>">
                        		<span class="fa fa-user"></span>
                        		<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getLastName()); ?>
                    		</a>
                        	<?php } else {?>
                        	<a href="/login.html" class="">Login</a>
                        	<?php }?>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse --> 
            </div>
            <!-- /.container-fluid --> 
        </nav>
        <!-- Header -->
        
		<section>
        <?php echo $_REQUEST[Page::ATT_VIEW]; ?>
		</section>
        
        <div id="footer">
            <div class="container text-center">
                  <div class="fnav">
                    <p>Copyright &copy; 2021 Shivalik. Designed by <a href="mailto:<?php echo htmlspecialchars($config->get('designerEmail')); ?>" rel="nofollow"><?php echo htmlspecialchars($config->get('designerName')); ?></a></p>
                  </div>
            </div>
        </div>
        <script type="text/javascript" src="/js/jquery.js"></script> 
        <script type="text/javascript" src="/js/bootstrap.js"></script> 
        <script type="text/javascript" src="/js/app.js"></script>
    </body>
</html>