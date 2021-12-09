<?php
use Applications\Root\Modules\Settings\SettingsController;
?>

<?php if (isset($_REQUEST[SettingsController::ATT_GENERATIONS])) { ?>
<section class="panel">
    <header class="panel-heading">
    	Register grade
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
    		<div class="row">
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error':'');?>">
            			<label class="form-label" for="name-grade">Name <span class="text-danger">*</span></label>
            			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['grade'])? $_REQUEST['grade']->name:'');?>" id="name-grade" class="form-control" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['name'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['amount'])? 'has-error':'');?>">
            			<label class="form-label" for="amount-grade">Amount <span class="text-danger">*</span></label>
            			<input type="text" name="amount" value="<?php echo htmlspecialchars(isset($_REQUEST['grade'])? $_REQUEST['grade']->amount:'');?>" id="amount-grade" class="form-control" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['amount'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
            			<?php }?>
            		</div>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['percentage'])? 'has-error':'');?>">
            			<label class="form-label" for="percentage-grade">Percentage <span class="text-danger">*</span></label>
            			<input type="text" name="percentage" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['grade'])? $_REQUEST['grade']->percentage:'');?>" id="percentage-grade" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['percentage'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['percentage']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['maxGeneration'])? 'has-error':'');?>">
            			<label class="form-label" for="maxGeneration-grade">Max generation <span class="text-danger">*</span></label>
            			<select name="maxGeneration" class="form-control" id="maxGeneration-grade">
            				<?php foreach ($_REQUEST[SettingsController::ATT_GENERATIONS] as $generation) : ?>
            				<option value="<?php echo $generation->id; ?>" <?php echo htmlspecialchars((isset($_REQUEST['grade']) && $_REQUEST['grade']->maxGeneration!=null)? ($_REQUEST['grade']->maxGeneration->id==$generation->id? 'selected="selected"':''):(''));?>><?php echo htmlspecialchars($generation->name); ?></option>
            				<?php endforeach;?>
            			</select>
            			<?php if (isset($_REQUEST['errors']['maxGeneration'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['maxGeneration']);?></p>
            			<?php }?>
            		</div>
    			</div>
    		</div>
    		<div class="row">
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['icon'])? 'has-error':'');?>">
            			<label class="form-label" for="image-grade">Icon <span class="text-danger">*</span></label>
            			<input type="file" name="icon" id="image-grade" class="form-control"/>
            			<?php if (isset($_REQUEST['errors']['icon'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['icon']);?></p>
            			<?php }?>
            		</div>
    			</div>
    		</div>
    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save</button>
    		</div>
    	</form>
    </div>
</section>

<?php } else { ?>
<div class="alert alert-danger">
	<p>no generation save in the generation configuration</p>
</div>
<?php }?>

