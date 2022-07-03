<?php 
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\VirtualMoney;


/**
 * @var VirtualMoney $virtual
 */
$virtual = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEY];
?>
<section class="panel">
    <header class="panel-heading">
    	Send virtual money
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
    		
    		<?php if ($virtual->getSoldDebts()>0) :?>
    		<div class="alert alert-info">
    			<p><strong>Alert</strong> he has a $ <?php echo "{$virtual->getSoldDebts()}"; ?> membership debt. this amount will be deducted from the amount you have to send it</p>
    		</div>
    		<?php endif; ?>
    		
    		<div class="row">
    			<div class="col-md-6 col-sm-6 col-xs-12">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['product'])? 'has-error':'');?>">
            			<label class="form-label" for="field-virtual-product-amount">Product purchase amount</label>
        	    		<div class="input-group">
        	  				<span class="input-group-addon">$</span>
        	  				<input type="text" id="field-virtual-product-amount" class="form-control" name="product" value="<?php echo(isset($_REQUEST['virtualMoney'])? $_REQUEST['virtualMoney']->product : ''); ?>" placeholder="put amount to send" autocomplete="off" aria-label="Product purchase amount">
        				</div>
        				<?php if (isset($_REQUEST['errors']['product'])){?>
                        <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['product']);?></p>
                        <?php }?>
            		</div>
    			</div>
    			<div class="col-md-6 col-sm-6 col-xs-12">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['afiliate'])? 'has-error':'');?>">
            			<label class="form-label" for="field-virtual-product-amount">Affiliate account amount</label>
        	    		<div class="input-group">
        	  				<span class="input-group-addon">$</span>
        	  				<input type="text" id="field-virtual-afiliate-amount" class="form-control" name="afiliate" value="<?php echo(isset($_REQUEST['virtualMoney'])? $_REQUEST['virtualMoney']->afiliate : ''); ?>" placeholder="put amount to send" autocomplete="off" aria-label="Affiliate account amount">
        				</div>
        				<?php if (isset($_REQUEST['errors']['afiliate'])){?>
                        <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['afiliate']);?></p>
                        <?php }?>
            		</div>
    			</div>
    		</div>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Send</button>
    		</div>
    	</form>
    </div>
</section>