<section class="panel">
    <header class="panel-heading"> <?php echo (isset($_GET['rubricId'])? 'Update':'New') ?> budget rubric</header>
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
				<div class="row">
					<div class="col-md-4">
						<div class="form-group <?php echo (isset($_REQUEST['errors']['label'])? 'has-error':'');?>">
							<label class="form-label" for="field-label">Label of budget rubirc <span class="text-danger">*</span></label>
							<input type="text" name="label" value="<?php echo htmlspecialchars(isset($_REQUEST['rubric'])? $_REQUEST['rubric']->label:'');?>" id="field-label" class="form-control" autocomplete="off"/>
							<?php if (isset($_REQUEST['errors']['label'])){?>
							<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['label']);?></p>
							<?php }?>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group <?php echo (isset($_REQUEST['errors']['category'])? 'has-error':'');?>">
							<label class="form-label" for="category-select-field">Category<span class="text-danger">*</span></label>
							<select class="form-control" name="category" id="category-select-field">
								<?php foreach ($_REQUEST['categories'] as $category) : ?>
								<option value="<?php echo $category->id; ?>"> <?php echo htmlspecialchars($category->label); ?> </option>
								<?php endforeach; ?>
							</select>
							<?php if (isset($_REQUEST['errors']['category'])){?>
							<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['category']);?></p>
							<?php }?>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group <?php echo (isset($_REQUEST['errors']['owner'])? 'has-error':'');?>">
							<label class="form-label" for="field-owner">Member owner <span class="text-danger">(*)</span></label>
							<input type="text" name="owner" value="<?php echo htmlspecialchars((isset($_REQUEST['rubric']) && $_REQUEST['rubric']->owner )? $_REQUEST['rubric']->owner->matricule:'');?>" id="field-owner" class="form-control" autocomplete="off"/>
							<?php if (isset($_REQUEST['errors']['owner'])){?>
							<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['owner']);?></p>
							<?php }?>
						</div>
					</div>
				</div>

				<div class="form-group <?php echo (isset($_REQUEST['errors']['description'])? 'has-error':'');?>">
					<label class="form-label" for="field-decription">Description <span class="text-danger">*</span></label>
					<textarea class="form-control" name="description" id="field-description" cols="30" rows="3"></textarea>
					<?php if (isset($_REQUEST['errors']['description'])){?>
					<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['description']);?></p>
					<?php } ?>
				</div>
    		</fieldset>

    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save <?php echo (isset($_GET['categoryId'])? 'modification':'') ?></button>
    		</div>
    	</form>
    </div>
</section>
