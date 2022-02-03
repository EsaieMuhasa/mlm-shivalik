<?php 

use Core\Shivalik\Filters\SessionOfficeFilter;

$admin = $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION];
?>

<ul class="sidebar-menu">
	<li>
		<a href="/office/profil/" title="<?php echo htmlspecialchars("{$admin->getName()} {$admin->getPostName()} {$admin->getLastName()}"); ?>">
    		<span class="nav-item-icon">
        		<span class="fa fa-user"></span>
    		</span>
    		<span class="nav-item-caption">My profil</span>
		</a>
	</li>


	<li>
		<a href="/office/">
    		<span class="nav-item-icon">
        		<span class="fa fa-home"></span>
    		</span>
    		<span class="nav-item-caption">Dashboard</span>
		</a>
	</li>
	
	<li>
		<a href="/office/members/">
    		<span class="nav-item-icon">
        		<span class="fa fa-users"></span>
    		</span>
    		<span class="nav-item-caption">Members</span>
		</a>
	</li>
	
	<li>
		<a href="/office/virtualmoney/">
    		<span class="nav-item-icon">
        		<span class="fa fa-money"></span>
    		</span>
    		<span class="nav-item-caption">Virtual money</span>
		</a>
	</li>
	
	<li>
		<a href="/logout.html">
    		<span class="nav-item-icon">
        		<span class="icon_key_alt"></span>
    		</span>
    		<span class="nav-item-caption">Logout</span>
		</a>
	</li>
</ul>