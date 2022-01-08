<?php 

use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>

<section class="panel">
    <header class="panel-heading">
    	Virtual money request form
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
    			<div class="col-xs-12 col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['amount'])? 'has-error':'');?>">
            			<label class="form-label" for="amount-office">Amount <span class="text-danger">*</span></label>
            			<div class="input-group">
                			<input type="text" name="amount"  value="<?php echo htmlspecialchars(isset($_REQUEST['virtualMoney'])? $_REQUEST['virtualMoney']->amount:'');?>" id="amount-office" class="form-control" autocomplete="off"/>
            				<span class="input-group-addon"><?php echo $config->get('devise'); ?></span>
            			</div>
            			<?php if (isset($_REQUEST['errors']['amount'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-xs-12 col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
            			<label class="form-label" for="password">Password Confirmation <span class="text-danger">*</span></label>
            			<input type="password" name="password"  id="password" class="form-control" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['password'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['password']);?></p>
            			<?php }?>
            		</div>
    			</div>
    		</div>
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Send your request</button>
    		</div>
		</form>
	</div>
</section>