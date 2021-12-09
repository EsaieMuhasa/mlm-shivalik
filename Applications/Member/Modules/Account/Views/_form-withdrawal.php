<?php
use Applications\Member\Modules\Account\AccountController;
use Library\Config;

$config = Config::getInstance();

/**
 * @var \Library\Config\VarList $money
 */
$money = $config->get('virtualMoney');

/**
 * @var \Entities\Account $account
 */
$account = $_REQUEST[AccountController::ATT_ACCOUNT];
?>

<section class="panel">
    <header class="panel-heading">Withdrawal request</header>
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
            				<span class="input-group-addon"><?php echo $config->get('devise'); ?></span>
                			<input type="text" name="amount" <?php echo (isset($_GET['id'])? 'readonly="readonly"':''); ?>  value="<?php echo htmlspecialchars(isset($_REQUEST['withdrawal'])? $_REQUEST['withdrawal']->amount:'');?>" id="amount-office" class="form-control" autocomplete="off"/>
            				<?php if(!isset($_GET['id'])) :  ?>
            				<span class="input-group-addon">
            					<span class="text-danger"><?php echo "Max: {$account->getSolde()} {$config->get('devise')}"; ?></span>
        					</span>
        					<?php endif; ?>
            			</div>
            			<?php if (isset($_REQUEST['errors']['amount'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-xs-12 col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['telephone'])? 'has-error':'');?>">
            			<label class="form-label" for="telephone">Téléphone <span class="text-danger">*</span></label>
            			<input type="text" name="telephone"  id="telphone" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['withdrawal'])? ($_REQUEST['withdrawal']->telephone!=null? ($_REQUEST['withdrawal']->telephone) : (isset($_REQUEST['member'])? $_REQUEST['member']->telephone:'')):((isset($_REQUEST['member'])? $_REQUEST['member']->telephone:'')));?>" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['telephone'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['telephone']);?></p>
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
    		
    		
    		<fieldset>
    			<legend>Office<span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-12">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['office'])? 'has-error':'');?>">                			
                			<?php if (isset($_REQUEST['errors']['office'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['office']);?></p>
                			<?php }?>
                			<div class="row">
                    			<?php  foreach ($_REQUEST[AccountController::ATT_OFFICES] as $office) : ?>
                    			<label class="col-lg-3 col-md-3 col-sm-4 col-xs-6" for="office-<?php echo $office->id;?>" style="padding-bottom: 30px;">
                    				<span class="thumbnail">
                    					<input type="radio" name="office" value="<?php echo $office->id; ?>" id="office-<?php echo $office->id;?>" <?php echo (isset($_REQUEST['withdrawal']) && $_REQUEST['withdrawal']->office != null && $office->id == $_REQUEST['withdrawal']->office->id)? ' checked="checked"':''; ?>/>
                    					<strong><?php echo htmlspecialchars($office->name); ?></strong>
                    					<img alt="" class="img-responsive" src="/<?php echo ($office->photo); ?>">
                    				</span>
                    			</label>
                    			<?php endforeach; ?>
                			</div>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		
    		<fieldset>
    			<legend>Other possibility </legend>
    			<div class="row">
    				<div class="col-md-12">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['auther'])? 'has-error':'');?>">                			
                			<div class="row">
                				
                				<?php foreach ($money->getItems() as $item) :?>
                    			<label class="col-lg-3 col-md-3 col-sm-4 col-xs-6" for="money-<?php echo $item->getName();?>" style="padding-bottom: 15px;">
                    				<span class="thumbnail">
                    					<input type="radio" name="office" value="<?php echo $item->getName(); ?>" id="money-<?php echo $item->getName();?>"/>
                    					<strong><?php echo htmlspecialchars($item->getValue()); ?></strong>
                    					<img alt="" class="img-responsive" src="<?php echo ($config->get("icone_{$item->getName()}")); ?>">
                    				</span>
                    			</label>
                    			<?php endforeach; ?>
                    			
                			</div>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Send your request</button>
    		</div>
    	</form>
    </div>
</section>


