
<section class="panel">
    <header class="panel-heading">
    	Administrator account password reset form
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo ($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
    		<fieldset>
        		<legend>Reset account password</legend>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="password-officeAdmin">Password <span class="text-danger">*</span></label>
                			<input type="password" name="password" id="password-officeAdmin" class="form-control" placeholder="put here the default password" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['password'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['password']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="confirmation-officeAdmin">Confirmation <span class="text-danger">*</span></label>
                			<input type="password" name="confirmation" id="confirmation-officeAdmin" class="form-control" placeholder="here confirmation of default password" autocomplete="off"/>
                		</div>
        			</div>
        		</div>
        		
    		</fieldset>
    		
    		<div class="text-center">
        		<button class="btn btn-primary">Save this new password</button>
    		</div>
    	</form>
    </div>
</section>