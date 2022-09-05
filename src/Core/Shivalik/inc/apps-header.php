<?php 


use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\OfficeAdmin;
use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;


$user = isset($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION])? ($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION]) : 
    (isset($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION])? ($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION]) : 
    (isset($_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION])? $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION] : null));
?>

<header class="header dark-bg">
    <div class="toggle-nav">
    	<div class="icon-reorder tooltips" data-original-title="Menu" data-placement="bottom"><i class="icon_menu"></i></div>
    </div>
    <a href="/" class="logo">Shivalik<span class="lite">Herbals</span></a>
    <div class="top-nav notification-row">
    	<ul class="nav pull-right top-menu">
    		<!-- inbox notificatoin start-->
    		<!-- alert notification start-->
            <li id="alert_notificatoin_bar" class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <i class="icon-bell-l"></i>
                    <span class="badge bg-important">7</span>
                </a>
                <ul class="dropdown-menu extended notification">
                    <li class="notify-arrow notify-arrow-blue"></li>
                    <li>
                        <p class="blue">You have 4 new notifications</p>
                    </li>
                    <li>
                        <a href="#">
                            <span class="label label-primary"><i class="icon_profile"></i></span> Friend Request
                            <span class="small italic pull-right">5 mins</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="label label-warning"><i class="icon_pin"></i></span> John location.
                            <span class="small italic pull-right">50 mins</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="label label-danger"><i class="icon_book_alt"></i></span> Project 3 Completed.
                            <span class="small italic pull-right">1 hr</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="label label-success"><i class="icon_like"></i></span> Mick appreciated your work.
                            <span class="small italic pull-right"> Today</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">See all notifications</a>
                    </li>
                </ul>
            </li>
            <!-- alert notification end-->
    		<li class="dropdown">
    			<a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="profile-ava">
                        <img style="width: 30px;" alt="" src="<?php echo htmlspecialchars($user!=null? ("/{$user->getPhoto()}") :'/img/user.png'); ?>">
                    </span>
                    <span class="username"><?php echo htmlspecialchars($user!=null? ("{$user->getLastName()} {$user->getPostName()}") :'Root'); ?></span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
                	<li class="log-arrow-up"></li>
                    <li class="eborder-top">
                    	<a href="/<?php echo ($user!=null && ($user instanceof Member))? "member":(($user instanceof OfficeAdmin)? ($user->getOffice()->isCentral()? "admin":"office"):("")); ?>/profil/"><i class="icon_profile"></i> My profil</a>
                    </li>
                    <li>
                    	<a href="/logout.html"><i class="icon_key_alt"></i> Logout</a>
                    </li>
                </ul>
            </li>
    	</ul>
    </div>
</header>