<?php
use Applications\Common\Modules\Index\IndexController;
use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;
use PHPBackend\AppConfig;
use PHPBackend\Page;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$admin = isset($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION])? $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION] : null;//admin centrale
$office = isset($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION])? $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION] : null;//admin d'un office secondaire
$member = isset($_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION])? $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION] : null;//membre adherant
$activeMenu = isset($_REQUEST[IndexController::ACTIVE_ITEM_MENU])? $_REQUEST[IndexController::ACTIVE_ITEM_MENU] : '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <title>Shivalik Herbals</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/css/style.css"/>
        <link rel="icon" type="image/jpg" href="/img/logo-32x32.png" />
        
        <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body>
        <!-- .container-fluid --> 

        <div class="container-fluid">
            <nav class="default-navbar">
                <div class="container"> 
                    <div class="default-navbar-header">
                        <a href="/">
                            <img src="/img/logo-50x50.png" class="-hidden-xs" alt="">
                            <!-- <img src="/logo-32x32.png" class="visible-xs" alt=""> -->
                            <strong class="header-txt">
                                <span class="txt-logo-1">Shivalik</span><span class="txt-logo-2">Herbals</span>
                            </strong>
                        </a>
                    </div>

                    <div class="default-nav-xs">
                        <button class="toggle-sm-screen">
                            <span class="fa fa-menu"></span>
                        </button>
                    </div>
                        
                    <ul class="default-nav">
                        <li><a href="/" class="<?php echo ($activeMenu == IndexController::ITEM_MENU_HOME? 'active' : ''); ?>">Home</a></li>
                        <li><a href="/products/">Products</a></li>
                        <li><a href="/news/">News</a></li>
                        <li><a href="/about.html" class="<?php echo ($activeMenu == IndexController::ITEM_MENU_ABOUT? 'active' : ''); ?>">About</a></li>
                        <li><a href="/contact.html" class="<?php echo ($activeMenu == IndexController::ITEM_MENU_CONTACT? 'active' : ''); ?>">Contact</a></li>
                        <?php if ($admin == null && $office == null && $member == null) : ?>
                        <li>
                            <a href="/login.html">Login</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            <!-- Header -->
            
            <?php echo $_REQUEST[Page::ATT_VIEW]; ?>
            
            <footer class="default-footer">
                <div class="container text-center">
                    <p>Copyright &copy; 2021 Shivalik. Designed by <a href="mailto:<?php echo htmlspecialchars($config->get('designerEmail')); ?>" rel="nofollow"><?php echo htmlspecialchars($config->get('designerName')); ?></a></p>
                </div>
            </footer>
            
        </div>
        <!-- /.container-fluid --> 
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/main.js"></script>
    </body>
</html>