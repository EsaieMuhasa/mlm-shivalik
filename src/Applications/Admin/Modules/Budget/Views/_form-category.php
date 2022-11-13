<section class="panel">
    <header class="panel-heading"> <?php echo (isset($_GET['categoryId'])? 'Update':'New') ?> Category</header>
    <div class="panel-body">
    	<form role="form" action="" method="POST">
    		<?php if (isset($_REQUEST['result'])) : ?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<strong><?php echo ($_REQUEST['result']);?></strong>
    			<?php if (isset($_REQUEST['errors']['message'])) : ?>
    			<p><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    			<?php endif;?>
    		</div>
    		<?php endif;?>
    			
    		<fieldset>
				<div class="form-group <?php echo (isset($_REQUEST['errors']['label'])? 'has-error':'');?>">
					<label class="form-label" for="field-label">Label of category <span class="text-danger">*</span></label>
					<input type="text" name="label" value="<?php echo htmlspecialchars(isset($_REQUEST['category'])? $_REQUEST['category']->label:'');?>" id="field-label" class="form-control" autocomplete="off"/>
					<?php if (isset($_REQUEST['errors']['label'])){?>
					<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['label']);?></p>
					<?php }?>
				</div>
				<div class="form-group <?php echo (isset($_REQUEST['errors']['description'])? 'has-error':'');?>">
					<label class="form-label" for="field-decription">Description <span class="text-danger">*</span></label>
					<textarea class="form-control" name="description" id="field-description" cols="30" rows="10"></textarea>
					<?php if (isset($_REQUEST['errors']['description'])){?>
					<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['description']);?></p>
					<?php } ?>
				</div>

				<div class="form-group">
					<label for="ownable-field" class="form-label">
						<input type="checkbox" name="ownable" id="ownable-field" value="ownable"> require owner account
					</label>
				</div>
    		</fieldset>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save <?php echo (isset($_GET['categoryId'])? 'modification':'') ?></button>
    		</div>
    	</form>
    </div>
</section>