<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Validators\LocalisationFormValidator;
?>
<fieldset>
	<legend>Membrer's address</legend>
	<div class="row">
		<div class="col-md-6">
    		<div class="form-group <?php echo (isset($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCountry)? 'has-error':'');?>">
    			<label class="form-label" for="localisation-country">Country <span class="text-danger">*</span></label>
    			
    			<select name="country" id="localisation-country" class="form-control" >
        			<?php  foreach ($_REQUEST[SettingsController::ATT_COUNTRYS] as $country) : ?>
        			<option value="<?php echo $country->id; ?>" title="<?php echo htmlspecialchars($country->abbreviation); ?>">
        				<?php echo htmlspecialchars($country->name); ?>
    				</option>
        			<?php endforeach; ?>
    			</select>
    			<?php if (isset($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCountry)){?>
    			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCountry);?></p>
    			<?php }?>
    		</div>
		</div>
		<div class="col-md-6">
    		<div class="form-group <?php echo (isset($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCity)? 'has-error':'');?>">
    			<label class="form-label" for="localisation-city">City <span class="text-danger">*</span></label>
    			<input type="text" name="city" id="localisation-city" class="form-control" autocomplete="pff" value="<?php echo htmlspecialchars(isset($_REQUEST['localisation'])? $_REQUEST['localisation']->city:'');?>"/>
    			<?php if (isset($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCity)){?>
    			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorCity);?></p>
    			<?php }?>
    		</div>
		</div>
		<div class="col-md-6">
    		<div class="form-group <?php echo (isset($_REQUEST[LocalisationFormValidator::LOCALISATION_FEEDBACK]->errorDistrict)? 'has-error':'');?>">
    			<label class="form-label" for="localisation-district">District </label>
    			<input type="text" name="district" id="localisation-district" class="form-control" autocomplete="off" value="<?php echo htmlspecialchars(isset($_REQUEST['localisation'])? $_REQUEST['localisation']->district:'');?>"/>
    			<?php if (isset($_REQUEST['errors']['district'])){?>
    			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['district']);?></p>
    			<?php }?>
    		</div>
		</div>
	</div>
</fieldset>