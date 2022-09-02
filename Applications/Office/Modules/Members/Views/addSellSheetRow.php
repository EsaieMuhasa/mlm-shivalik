<?php

use Applications\Office\Modules\Members\MembersController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product [] $products
 */
$products = $_REQUEST[MembersController::ATT_PRODUCTS];
?>

<section class="panel">
    <header class="panel-heading">
    	Register a new sell sheet row
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST">
            <?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['product'])? 'has-error':'');?>">
            			<label class="form-label" for="product-field">Select product <span class="text-danger">*</span></label>
            			<select name="product" onchange="selectionChange();" id="product-field" class="form-control" >
                        <?php foreach ($products as $product) : ?>
                            <option data-price="<?php echo $product->defaultUnitPrice; ?>" value="<?php echo $product->id; ?>"><?php echo htmlspecialchars($product->name); ?></option>
                        <?php endforeach; ?>
                        </select>
                        <?php if (isset($_REQUEST['errors']['product'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['product']);?></p>
            			<?php }?>
            		</div>
    			</div>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="form-group <?php echo (isset($_REQUEST['errors']['quantity'])? 'has-error':'');?>">
                        <label class="form-label" for="quantity-field">Quantity <span class="text-danger">*</span></label>
                        <input type="number" onchange="calculTotal();" onkeyup="calculTotal();" name="quantity"  id="quantity-field" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['sellSheetRow'])? ($_REQUEST['sellSheetRow']->quantity) : '1');?>" min="1" autocomplete="off"/>
                        <?php if (isset($_REQUEST['errors']['quantity'])){?>
                            <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['quantity']);?></p>
                        <?php }?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="form-group <?php echo (isset($_REQUEST['errors']['unitPrice'])? 'has-error':'');?>">
                        <label class="form-label" for="unitPrice-field">Unit price<span class="text-danger">*</span></label>
                        <input readonly type="text" onchange="calculTotal();" onkeyup="calculTotal();" name="unitPrice"  id="unitPrice-field" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['sellSheetRow'])? ($_REQUEST['sellSheetRow']->unitPrice) : $products[0]->getDefaultUnitPrice());?>" autocomplete="off"/>
                        <?php if (isset($_REQUEST['errors']['unitPrice'])){?>
                            <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['unitPrice']);?></p>
                        <?php }?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2">
                    <div class="form-group <?php echo (isset($_REQUEST['errors']['totalPrice'])? 'has-error':'');?>">
                        <label class="form-label" for="totalPrice-field">Total price<span class="text-danger">*</span></label>
                        <input readonly type="text" name="totalPrice"  id="totalPrice-field" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['sellSheetRow'])? ($_REQUEST['sellSheetRow']->totalPrice) : $products[0]->getDefaultUnitPrice());?>" autocomplete="off"/>
                        <?php if (isset($_REQUEST['errors']['totalPrice'])){?>
                            <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['totalPrice']);?></p>
                        <?php }?>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button class="btn btn-primary">Save</button>
    		</div>
        </form>
    </div>
</section>
<script>
    
    function calculTotal () {
        var quantity = Number.parseFloat($('#quantity-field').val());
        var unitPrice =  Number.parseFloat($('#unitPrice-field').val());
        var total = unitPrice * quantity;
        $('#totalPrice-field').val(isNaN(total)? '-' : total);
    };
    
    function selectionChange () {
        var option = $("#product-field option:selected");
        var price = Number.parseFloat(option.attr('data-price'));
        $('#unitPrice-field').val(price);
        calculTotal();
    };

</script>