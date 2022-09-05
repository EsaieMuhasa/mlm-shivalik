<?php 
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<div class="alert alert-danger">
	<p>
    	<strong><span class="icon_comment"></span> Warning : </strong>
		When using this form, the purchase bonus is transmitted instantly. An other, this operation is irreversible.
	</p>
</div>
<section class="panel">
    <header class="panel-heading">Form to perform monthly purchase bonus operation</header>
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
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['memberId'])? 'has-error':'');?>">
            			<label class="form-label" for="memberId">Member ID <span class="text-danger">*</span></label>
            			<input type="text" name="memberId"  id="memberId" class="form-control" value="<?php echo ((isset($_REQUEST['monthlyOrder']) && $_REQUEST['monthlyOrder']->member!=null)? ($_REQUEST['monthlyOrder']->member->matricule) : (''));?>" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['memberId'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['memberId']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-xs-12 col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['amount'])? 'has-error':'');?>">
            			<label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
            			<div class="input-group">
            				<span class="input-group-addon"><?php echo $config->get('devise'); ?></span>
                			<input type="text" name="amount"  value="<?php echo htmlspecialchars(isset($_REQUEST['monthlyOrder'])? $_REQUEST['monthlyOrder']->amount:'');?>" id="amount" class="form-control" autocomplete="off"/>
            			</div>
            			<?php if (isset($_REQUEST['errors']['amount'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
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