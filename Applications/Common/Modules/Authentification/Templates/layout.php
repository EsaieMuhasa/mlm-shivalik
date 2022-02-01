<?php
use PHPBackend\Page;
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
        <link rel="icon" type="image/jpg" href="/logo-32x32.png" />
        
        <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body class="login-page">
        <!-- .container-fluid --> 

        <div class="container-fluid">
            <nav class="default-navbar">
                <div class="container"> 
                    <div class="default-navbar-header">
                        <a href="/">
                            <img src="/img/logo-50x50.png" alt="">
                            <strong class="header-txt">
                                <span class="txt-logo-1">Shivalik</span><span class="txt-logo-2">Herbals</span>
                            </strong>
                        </a>
                    </div>
                </div>
            </nav>
            <!-- Header -->

            <div class="row">
                <?php echo $_REQUEST[Page::ATT_VIEW];?>
            </div>
            
            <footer class="login-footer">
                <nav class="navbar navbar-default navbar-fixed-bottom">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="">
                                <span class="fa fa-book"></span>
                                <span class="hidden-xs"> Politics</span>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <span class="fa fa-help-circled"></span>
                                <span class="hidden-xs"> Help</span>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <span class="fa fa-address-book"></span>
                                <span class="hidden-xs"> Contact</span>
                            </a>
                        </li>
                        <li>
                            <a href="/about.html">
                                <span class="fa fa-info-circled"></span>
                                <span class="hidden-xs"> About</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </footer>
        </div>
        <!-- /.container-fluid --> 
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/main.js"></script>
    </body>
</html>

<?php exit();?>