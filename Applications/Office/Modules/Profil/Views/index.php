<?php 

use Applications\Office\OfficeApplication;

$admin = OfficeApplication::getConnectedUser();
?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-user"></i> My profil</h3>
    </div>
</div>

<div class="row">
	<div class="col-xs-12 text-center">
		<span class="btn-group">
    		<a href="password.html" class="btn btn-danger">Update password</a>
    		<a href="photo.html" class="btn btn-primary">Update photo</a>
		</span>
	</div>
</div>
<hr/>

<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-4 col-sm-offset-0 col-xs-10 col-xs-offset-1">
		<span class="thumbnail">
			<img class="img-responsive" alt="" src="/<?php echo $admin->getPhoto(); ?>"/>
		</span>
	</div>
	
	<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
		<h1><?php echo $admin->getEmail(); ?></h1>
		<hr/>
		<h4>Name: <?php echo htmlspecialchars($admin->getName()); ?></h4>
		<h4>Post name: <?php echo htmlspecialchars($admin->getPostName()); ?></h4>
		<h4>Last name: <?php echo htmlspecialchars($admin->getLastName()); ?></h4>
		<hr/>
		<h4 class="text-info">Telephone: <?php echo htmlspecialchars($admin->getTelephone()); ?></h4>
		<h4 class="text-info">Email: <?php echo htmlspecialchars($admin->getEmail()); ?></h4>
	</div>
	
	<div class="col-xs-12">
		<hr/>
		<fieldset>
			<legend>Address</legend>
			<h4>Country: <?php echo htmlspecialchars($admin->getLocalisation()->getCountry()->getName()); ?></h4>
    		<h4>City: <?php echo htmlspecialchars($admin->getLocalisation()->getCity()); ?></h4>
    		<h4>District: <?php echo htmlspecialchars($admin->getLocalisation()->getDistrict()); ?></h4>
    		
    		<a href="address.html" class="btn btn-primary">Update address</a>
		</fieldset>
	</div>
</div>