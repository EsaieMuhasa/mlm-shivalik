<section class="panel">
    <header class="panel-heading">
    	<strong><span class="text-danger">Step 1</span> >> Choose member</strong>
    	<p class="text-info">below, enter the ID of the member making the order</p>
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
    			<legend>Member ID<span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-6">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['matricul'])? 'has-error':'');?>">
                			<label class="form-label hidden" for="member-matricul">ID</label>
                			<input type="text" name="matricul" id="member-matricul" class="form-control" placeholder="put the member ID" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['matricul'])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['matricul']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		<div class="form-group text-center">
    			<button class="btn btn-primary" type="submit"><span class="fa fa-forward"></span> Next</button>
    		</div>
		</form>
	</div>
</section>