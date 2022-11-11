<section class="panel">
    <header class="panel-heading"> <?php echo (isset($_GET['categoryId'])? 'Update':'New') ?> Category.</header>
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
    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['quantity'])? 'has-error':'');?>">
                			<label class="form-label" for="stock-quantity">Quantity <span class="text-danger">*</span></label>
                			<input type="number" name="quantity" value="<?php echo htmlspecialchars(isset($_REQUEST['stock'])? $_REQUEST['stock']->quantity:'');?>" id="stock-quantity" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['quantity'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['quantity']);?></p>
                			<?php }?>
                		</div>
    				</div>
    				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['parent'])? 'has-error':'');?>">
                			<label class="form-label" for="product-parent">Product stocks<span class="text-danger">*</span></label>
                			
                			<select name="parent" id="product-parent" class="form-control">
                				<?php foreach ($stocks as $stock) : ?>
                				<option value="<?php echo $stock->id; ?>" <?php echo ($auxiliary != null && $auxiliary->parent != null && $auxiliary->parent->id == $stock->id)? " selected=\"selected\"":""; ?>>
                					<?php echo htmlspecialchars($stock->product->name); ?> => ExpiryDate <?php echo htmlspecialchars($stock->expiryDate->format('d/m/Y')); ?>, sold: <?php echo ($stock->sold); ?>
            					</option>
                				<?php endforeach;?>
                			</select>
                			
                			<?php if (isset($_REQUEST['errors']['parent'])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['parent']);?></p>
                			<?php }?>
                		</div>
    				</div>
    			</div>
    		</fieldset>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save <?php echo (isset($_GET['stockId'])? 'modification':'') ?></button>
    		</div>
    	</form>
    </div>
</section>