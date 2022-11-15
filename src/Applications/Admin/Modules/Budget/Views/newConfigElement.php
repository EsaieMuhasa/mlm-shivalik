<section class="panel">
    <header class="panel-heading">Budget configuration</header>
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

			<div class="row">
				<?php foreach ($_REQUEST['elements'] as $item) : ?>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
							<label class="form-label" for="field-element-<?php echo $item->id; ?>"><?php echo $item->label; ?> <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" name="element<?php echo $item->id; ?>" value="<?php echo htmlspecialchars(isset($_POST["element{$item->id}"])? $_POST["element{$item->id}"] : (100 / count($_REQUEST['elements']))); ?>" id="field-element-<?php echo $item->id; ?>" class="form-control" autocomplete="off"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary">
					<span class="fa fa-save"></span> Save
				</button>
				<a href="/admin/budget/new/cancel-element-config" class="btn btn-danger">
					<span class="glyphicon glyphicon-remove"></span> Cancel
				</a>
    		</div>
    	</form>
    </div>
</section>