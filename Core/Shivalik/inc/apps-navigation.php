<?php

use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;

$admin = isset($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION])? $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION] : null;//admin centrale
$office = isset($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION])? $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION] : null;//admin d'un office secondaire
$member = isset($_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION])? $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION] : null;//membre adherant
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
		} elseif (false) {
	        require_once 'navs-root.php';
	    }
		?>
    </nav>
</aside>