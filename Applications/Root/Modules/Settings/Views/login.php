<?php

/**
 * @var AppConfig $config
 */
use PHPBackend\AppConfig;
use PHPBackend\Request;

$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Shivalik internationnal">
        <meta name="author" content="Ir Esaie MUHASA">
        <meta name="keyword" content="MLM, Admin, RDC, Medical">
        <link rel="shortcut icon" href="img/favicon.png">
		<title>Shivalik members login</title>
        <link rel="icon" type="image/png" href="/logo.png" />
    
        <!-- Bootstrap CSS -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <!-- bootstrap theme -->
        <link href="/css/bootstrap-theme.css" rel="stylesheet">
        <!--external css-->
        <!-- font icon -->
        <link href="/css/elegant-icons-style.css" rel="stylesheet" />
        <link href="/css/font-awesome.css" rel="stylesheet" />
        <!-- Custom styles -->
        <link href="/css/style.css" rel="stylesheet">
        <link href="/css/style-responsive.css" rel="stylesheet" />
        
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 -->
        <!--[if lt IE 9]>
            <script src="js/html5shiv.js"></script>
            <script src="js/respond.min.js"></script>
        <![endif]-->
    
        <!-- =======================================================
          Theme Name: NiceAdmin
          Theme URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
          Author: BootstrapMade
          Author URL: https://bootstrapmade.com
        ======================================================= -->
    </head>
    <body>
    
        <div class="container">
        	<div class="row">
        		<div class="col-lg-6 col-md-6 col-sm-8 col-xs-10 col-lg-offset-3 col-md-offset-3 col-sm-offset-2 col-xs-offset-1" style="padding-top: 50px;">
                    <form class="jumbotron" action="" method="post">
                        <div class="text-center" style="padding-bottom: 25px;">
                        	<img alt="" src="/img/logo-50x50.png"/>
                        </div>
                        <?php if (isset($_REQUEST['result'])){?>
                        	<div class="alert alert-danger">
                        		<strong class="text-danger text-center">
                        			<?php echo ($_REQUEST['result']);?>
                        		</strong>
                        		<?php if (isset($_REQUEST['errors']['message'])){?>
                        		<br/><span><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></span>
                        		<?php }?>
                        	</div>
                    	<?php }?>
                    	<div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon_profile"></i></span>
                                <input type="text" name="pseudo" class="form-control input-lg" placeholder="Username" value="<?php echo htmlspecialchars(isset($_REQUEST['user'])? $_REQUEST['user']->getPseudo(): ''); ?>" autofocus>
                            </div>
                    	</div>
                    	
                    	<div class="form-group">
                            <div class="input-group">
                            	<span class="input-group-addon"><i class="icon_key_alt"></i></span>
                            	<input type="password" name="password" class="form-control input-lg" placeholder="Password">
                            </div>
                    	</div>
                    	
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
                    </form>
        		</div>
        	</div>
            
            <div class="text-center">
        		<div class="credits">
                 	Designed by <a href="mailto:<?php echo $config->get('designerEmail'); ?>"><?php echo $config->get('designerName'); ?></a>
        		</div>
            </div>
        </div>
    
    </body>
</html>

<?php exit();?>