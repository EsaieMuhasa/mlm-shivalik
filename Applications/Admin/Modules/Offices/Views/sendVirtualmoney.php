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
    		
    		<div class="form-group <?php echo (isset($_REQUEST['errors']['amount'])? 'has-error':'');?>">
	    		<div class="input-group">
	  				<span class="input-group-addon">$</span>
	  				<input type="text" class="form-control" name="amount" value="<?php echo(isset($_REQUEST['virtualMoney'])? $_REQUEST['virtualMoney']->amount : ''); ?>" placeholder="put amount to send" autocomplete="off" aria-label="put amount to send">
	  				<span class="input-group-btn">	  				
		        		<button class="btn btn-primary"><span class="fa fa-send"></span> Send</button>
	  				</span>
				</div>
				<?php if (isset($_REQUEST['errors']['amount'])){?>
                <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
                <?php }?>
    		</div>
    		    		
    		<div class="text-center">
    		</div>
    	</form>
    </div>
</section>