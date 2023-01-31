<?php 


use Core\Shivalik\Filters\SessionAdminFilter;

$admin = $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION];
?>

<ul class="sidebar-menu">
	<li>
		<a href="/admin/profil/" title="<?php echo htmlspecialchars("{$admin->getName()} {$admin->getPostName()} {$admin->getLastName()}"); ?>">
    		<span class="nav-item-icon">
        		<span class="fa fa-user"></span>
    		</span>
    		<span class="nav-item-caption">My profil</span>
		</a>
	</li>

	<li>
		<a href="/admin/">
    		<span class="nav-item-icon">
        		<span class="fa fa-home"></span>
    		</span>
    		<span class="nav-item-caption">Dashbord</span>
		</a>
	</li>

	<li>
		<a href="/admin/raports/">
    		<span class="nav-item-icon">
        		<span class="fa fa-book"></span>
    		</span>
    		<span class="nav-item-caption">Raports</span>
		</a>
	</li>
	
	<li>
		<a href="/admin/members/">
    		<span class="nav-item-icon">
        		<span class="fa fa-users"></span>
    		</span>
    		<span class="nav-item-caption">Members</span>
		</a>
	</li>

	<li>
		<a href="/admin/check-members/">
    		<span class="nav-item-icon">
        		<span class="fa fa-question-circle"></span>
    		</span>
    		<span class="nav-item-caption">Check Members</span>
		</a>
	</li>
	
	<li>
		<a href="/admin/offices/">
    		<span class="nav-item-icon">
        		<span class="fa fa-briefcase"></span>
    		</span>
    		<span class="nav-item-caption">Offices</span>
		</a>
	</li>
	
	<li>
		<a href="/admin/products/">
    		<span class="nav-item-icon">
        		<span class="fa fa-leaf"></span>
    		</span>
    		<span class="nav-item-caption">Products</span>
		</a>
	</li>
	
	<li>
		<a href="/admin/budget/">
    		<span class="nav-item-icon">
        		<span class="icon_cog"></span>
    		</span>
    		<span class="nav-item-caption">Budget configuration</span>
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