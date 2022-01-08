<?php 

use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\OfficeAdmin;

if (AdminApplication::getConnectedUser() != null) {
    $user = AdminApplication::getConnectedUser();
} elseif (MemberApplication::getConnectedMember() != null){
    $user = MemberApplication::getConnectedMember();
}elseif (OfficeApplication::getConnectedUser() != null){
	$user = OfficeApplication::getConnectedUser();
} else {
    $user = null;
}
?>

<header class="header dark-bg">
    <div class="toggle-nav">
    	<div class="icon-reorder tooltips" data-original-title="Menu" data-placement="bottom"><i class="icon_menu"></i></div>
    </div>
    <a href="/" class="logo">Shivalik<span class="lite">Herbals</span></a>
    <div class="top-nav notification-row">
    	<ul class="nav pull-right top-menu">
    		<li class="dropdown">
    			<a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="profile-ava">
                        <img style="width: 30px;" alt="" src="<?php echo htmlspecialchars($user!=null? ("/{$user->getPhoto()}") :'/img/user.png'); ?>">
                    </span>
                    <span class="username"><?php echo htmlspecialchars($user!=null? ("{$user->getLastName()} {$user->getPostName()}") :'Root'); ?></span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
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