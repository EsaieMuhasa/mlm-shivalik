<?php 
use Applications\Member\MemberApplication;

$member = MemberApplication::getConnectedMember();

?>
<ul class="sidebar-menu">
	
	<li>
		<a href="/member/profil/">
    		<span class="nav-item-icon">
        		<span class="fa fa-user"></span>
    		</span>
    		<span class="nav-item-caption">My profil</span>
		</a>
	</li>
	
	<?php if ($member->getOfficeAccount() != null) : ?>
	<li>
		<a href="/member/office/">
    		<i class="icon_desktop"></i>
    		<span class="nav-item-caption">My Office</span>
		</a>
	</li>
	<?php endif; ?>
	

	<li>
		<a href="/member/">
    		<span class="nav-item-icon">
        		<span class="fa fa-home"></span>
    		</span>
    		<span class="nav-item-caption">Dashbord</span>
		</a>
	</li>
	
	<li>
		<a href="/member/downlines/">
    		<span class="nav-item-icon">
        		<span class="fa fa-users"></span>
    		</span>
    		<span class="nav-item-caption">Downlines</span>
		</a>
	</li>
	
	<li>
		<a href="/member/withdrawals/">
    		<span class="nav-item-icon">
        		<span class="fa fa-money"></span>
    		</span>
    		<span class="nav-item-caption">Withdrawal</span>
		</a>
	</li>
	
	<li>
		<a href="/member/tree/">
    		<span class="nav-item-icon">
        		<span class="fa fa-sitemap"></span>
    		</span>
    		<span class="nav-item-caption">Tree</span>
		</a>
	</li>
	<li>
		<a href="/member/history/">
    		<span class="nav-item-icon">
        		<span class="fa fa-eye"></span>
    		</span>
    		<span class="nav-item-caption">History</span>
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