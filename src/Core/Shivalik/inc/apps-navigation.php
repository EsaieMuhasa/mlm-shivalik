<?php

use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Filters\SessionRootFilter;

$admin = isset($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION])? $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION] : null;//admin centrale
$office = isset($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION])? $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION] : null;//admin d'un office secondaire
$member = isset($_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION])? $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION] : null;//membre adherant
$root = isset($_SESSION[SessionRootFilter::ROOT_CONNECTED_SESSION])? $_SESSION[SessionRootFilter::ROOT_CONNECTED_SESSION] : null;//root admin
?>



<aside>
	<nav id="sidebar" class="nav-collapse">
		<?php 
		if ($member != null) { 
		    require_once 'navs-member.php';
		} elseif ($admin != null) {
		    require_once 'navs-admin.php';
		} elseif ($office != null ) {
		    require_once 'navs-office.php';
		} elseif ($root != null) {
	        require_once 'navs-root.php';
	    }
		?>
    </nav>
</aside>