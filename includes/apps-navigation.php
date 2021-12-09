<?php
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Root\RootApplication;
use Applications\Office\OfficeApplication;
?>

<aside>
	<nav id="sidebar" class="nav-collapse ">
		<?php if (MemberApplication::getConnectedMember() != null) { ?>
        <?php require_once 'navs-member.php'; ?>
        <?php } ?>
        
		<?php if (AdminApplication::getConnectedUser() != null) { ?>
        <?php require_once 'navs-admin.php'; ?>
        <?php } ?>
        
        <?php if (OfficeApplication::getConnectedUser() != null) { ?>
        <?php require_once 'navs-office.php'; ?>
        <?php } ?>
        
        <?php //require_once 'navs-office.php'; ?>
		<?php if (isset($_SESSION[RootApplication::ATT_CONNECTED_ROOT])) : ?>
        <?php require_once 'navs-root.php'; ?>
		<?php endif; ?>
    </nav>
</aside>