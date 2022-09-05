
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-user"></i> My profil</h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-user"></i>
    			<a href="/member/profil/">Profil</a>
			</li>
			<li>
    			<i class="fa fa-key"></i>
    			<span>Password</span>
			</li>
		</ol>
    </div>
</div>

<section class="panel">
    <header class="panel-heading">
    	<strong>Update your password</strong>
    	<p class="text-info">After the password modification operation you will be immediately logged out of the system. You will only have to log in again with your new password.</p>
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo ($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
    		<fieldset>
    			<legend>Update your password <span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-6">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['old'])? 'has-error':'');?>">
                			<label class="form-label" for="member-old-password">Your old password</label>
                			<input type="password" name="old" id="member-old-password" class="form-control" placeholder="put your courrent password" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['old'])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['old']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="member-old-password">New password <span class="text-danger">*</span></label>
                			<input type="password" name="password" id="member-old-password" class="form-control" placeholder="put your new  password" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['password'])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['password']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="member-old-password-confirmation">Confirmation <span class="text-danger">*</span></label>
                			<input type="password" name="confirmation" id="member-old-password-confirmation" class="form-control" placeholder="hire confirmation of your new password" autocomplete="off"/>
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