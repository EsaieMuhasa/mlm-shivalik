<?php 
use Applications\Admin\Modules\Members\MembersController;
use Library\Config;

$config = Config::getInstance();
?>

<section class="panel">
    <header class="panel-heading">
    	upgrade the account
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo ($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    			
    			<pre>
    			<?php var_dump($_REQUEST['errors']); ?>
    			</pre>
    		</div>
    		<?php }?>
    		<fieldset>
    			<legend>Member's rank requested<span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-12">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['grade'])? 'has-error':'');?>">                			
                			<div class="row">
                    			<?php  foreach ($_REQUEST[MembersController::ATT_GRADES] as $grade) : ?>
                    			<label class="col-lg-2 col-md-3 col-sm-4 col-xs-6" for="grade-<?php echo $grade->id;?>" style="padding-bottom: 30px;">
                    				<span class="thumbnail">
                    					<input type="radio" name="grade" value="<?php echo $grade->id; ?>" id="grade-<?php echo $grade->id;?>" <?php echo (isset($_POST['grade']) && $grade->id == $_POST['grade'])? ' checked="checked"':''; ?>/>
                    					<strong><?php echo htmlspecialchars($grade->name); ?></strong>
                    					<img alt="" class="img-responsive" src="/<?php echo ($grade->icon); ?>">
                    					<span style="display: block;">
                        					<span class="label label-info">
                        						<?php echo ("{$grade->amount} {$config->get('devise')}"); ?>
                        					</span>
                    					</span>
                    					
                    					<?php
                    					$membership = 30;
                    					$product = ($grade->amount-30);
                    					?>
					
										<span style="display: block;">
                        					<span class="label label-danger">
                        						<?php echo ("Membership: {$membership} {$config->get('devise')}"); ?>
                        					</span>
                    					</span>
                    					
                    					<span style="display: block;">
                        					<span class="label label-primary">
                        						<?php echo ("Product: {$product} {$config->get('devise')}"); ?>
                        					</span>
                    					</span>
                    				</span>
                    			</label>
                    			<?php endforeach; ?>
                			</div>
                			
                			<?php if (isset($_REQUEST['errors']['grade'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['grade']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		<div class="form-group text-center">
    			<button class="btn btn-primary" type="submit">Send request</button>
    		</div>
		</form>
	</div>
</section>