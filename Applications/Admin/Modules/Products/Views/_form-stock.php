<?php
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product $product
 */
$product = $_REQUEST[ProductsController::ATT_PRODUCT];
?>
<section class="panel">
    <header class="panel-heading"> <?php echo (isset($_GET['id'])? 'Update':'Creating') ?> Stock of  «<?php echo htmlspecialchars($product->name); ?>» product.</header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
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
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['unitPrice'])? 'has-error':'');?>">
                			<label class="form-label" for="product-unitPrice">Unit price<span class="text-danger">*</span></label>
                			<span class="input-group">
                    			<input type="text" name="unitPrice" value="<?php echo htmlspecialchars(isset($_REQUEST['stock'])? ($_REQUEST['stock']->unitPrice) : $product->getDefaultUnitPrice());?>" id="product-unitPrice" class="form-control" autocomplete="off"/>
                    			<span class="input-group-addon">$</span>
                			</span>
                			<?php if (isset($_REQUEST['errors']['unitPrice'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['unitPrice']);?></p>
                			<?php }?>
                		</div>
    				</div>
    			</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['expiryDate'])? 'has-error':'');?>">
                			<label class="form-label" for="product-expiryDate">Expiry date <span class="text-danger">*</span></label>
                			<input type="date" name="expiryDate" value="<?php echo htmlspecialchars((isset($_REQUEST['stock']) && $_REQUEST['stock']->expiryDate != null)? $_REQUEST['stock']->expiryDate->format('Y-m-d'):'');?>" id="stock-expiryDete" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['expiryDate'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['expiryDate']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['manufacturingDate'])? 'has-error':'');?>">
                			<label class="form-label" for="product-manufacturingDate">Manufacturing date <span class="text-danger">*</span></label>
                			<input type="date" name="manufacturingDate" value="<?php echo htmlspecialchars((isset($_REQUEST['stock']) && $_REQUEST['stock']->manufacturingDate != null)? $_REQUEST['stock']->manufacturingDate->format('Y-m-d'):'');?>" id="stock-manufacturingDate" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['manufacturingDate'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['manufacturingDate']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="form-group <?php echo (isset($_REQUEST['errors']['comment'])? 'has-error':'');?>">
        			<label class="form-label" for="product-comment">Comment</label>
        			<textarea rows="4" cols="20"name="comment" id="product-comment" class="form-control" placeholder="put here the short comment"><?php echo htmlspecialchars(isset($_REQUEST['stock'])? ($_REQUEST['stock']->comment):'');?></textarea>
        			<?php if (isset($_REQUEST['errors']['comment'])){?>
        			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['comment']);?></p>
        			<?php }?>
        		</div>
    		</fieldset>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save <?php echo (isset($_GET['id'])? 'modification':'') ?></button>
    		</div>
    	</form>
    </div>
</section>