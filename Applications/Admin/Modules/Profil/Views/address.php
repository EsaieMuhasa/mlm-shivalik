<?php
use Applications\Member\Modules\Profil\ProfilController;
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-user"></i> My profil</h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-user"></i>
    			<a href="/member/profil/">Profil</a>
			</li>
			<li>
    			<i class="fa fa-home"></i>
    			<span>Addess</span>
			</li>
		</ol>
    </div>
</div>

<section class="panel">
    <header class="panel-heading">
    	<strong>Update your profil picture</strong>
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo ($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
    		<div class="row">
        		<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['ountry'])? 'has-error':'');?>">
            			<label class="form-label" for="localisation-country">Country <span class="text-danger">*</span></label>
            			
            			<select name="country" id="localisation-country" class="form-control" >
                			<?php  foreach ($_REQUEST[ProfilController::ATT_COUNTRYS] as $country) : ?>
                			<option value="<?php echo $country->id; ?>" title="<?php echo htmlspecialchars($country->abbreviation); ?>">
                				<?php echo htmlspecialchars($country->name); ?>
            				</option>
                			<?php endforeach; ?>
            			</select>
            			<?php if (isset($_REQUEST['errors']['country'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['countryc']);?></p>
            			<?php }?>
            		</div>
        		</div>
        		<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['city'])? 'has-error':'');?>">
            			<label class="form-label" for="localisation-city">City <span class="text-danger">*</span></label>
            			<input type="text" name="city" id="localisation-city" class="form-control" autocomplete="pff" value="<?php echo htmlspecialchars(isset($_REQUEST['localisation'])? $_REQUEST['localisation']->city:'');?>"/>
            			<?php if (isset($_REQUEST['errors']['city'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['city']);?></p>
            			<?php }?>
            		</div>
        		</div>
        		<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['district'])? 'has-error':'');?>">
            			<label class="form-label" for="localisation-district">District </label>
            			<input type="text" name="district" id="localisation-district" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars(isset($_REQUEST['localisation'])? $_REQUEST['localisation']->district:'');?>"/>
            			<?php if (isset($_REQUEST['errors']['district'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['district']);?></p>
            			<?php }?>
            		</div>
        		</div>
        	</div>
    		<div class="form-group text-center">
    			<button class="btn btn-primary" type="submit">Send request</button>
    		</div>
		</form>
	</div>
</section>