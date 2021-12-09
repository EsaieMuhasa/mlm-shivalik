<?php
use Library\Config;
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
    <body class="login-img3-body">
    
        <div class="container">
        
            <form class="login-form" action="" method="post">
                <div class="login-wrap">
                    <p class="login-img"><i class="icon_lock_alt"></i></p>
                    <?php if (isset($_REQUEST['result'])){?>
                    	<div class="alert alert-danger">
                    		<h3 class="text-danger text-center"><?php echo ($_REQUEST['result']);?></h3>
                    		<?php if (isset($_REQUEST['errors']['message'])){?>
                    		<p class="text-danger"><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
                    		<?php }?>
                    	</div>
                	<?php }?>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon_profile"></i></span>
                        <input type="text" name="pseudo" class="form-control" placeholder="Username" value="<?php echo htmlspecialchars(isset($_REQUEST['user'])? $_REQUEST['user']->getPseudo(): ''); ?>" autofocus>
                    </div>
                    <div class="input-group">
                    	<span class="input-group-addon"><i class="icon_key_alt"></i></span>
                    	<input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" value="remember-me"> Remember me
                        <span class="pull-right"> <a href="/forgot-password.html"> Forgot Password?</a></span>
                    </label>
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
                    <a class="btn btn-info btn-lg btn-block" href="/signup.html">Signup</a>
                </div>
            </form>
            
            <div class="text-right">
        		<div class="credits">
                 	Designed by <a href="mailto:<?php echo Config::getInstance()->get('designerEmail'); ?>"><?php echo Config::getInstance()->get('designerName'); ?></a>
        		</div>
            </div>
        </div>
    
    </body>
</html>

<?php exit();?>