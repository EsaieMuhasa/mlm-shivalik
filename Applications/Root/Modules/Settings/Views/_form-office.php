<section class="panel">
    <header class="panel-heading">
    	Register Office
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
    		<fieldset>
    			<legend>Identification</legend>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error':'');?>">
                			<label class="form-label" for="name-office">Name <span class="text-danger">*</span></label>
                			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['office'])? $_REQUEST['office']->name:'');?>" id="name-office" class="form-control" placeholder="put here the office name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['name'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['photo'])? 'has-error':'');?>">
                			<label class="form-label" for="photo-office">Photo <span class="text-danger">*</span></label>
                			<input type="file" name="photo" id="photo-office" class="form-control"/>
                			<?php if (isset($_REQUEST['errors']['photo'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['photo']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-12">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['central'])? 'has-error':'');?>">
                			<label class="btn btn-info" for="central-office">
    	            			<input type="checkbox" name="central" value="central" <?php echo htmlspecialchars((isset($_REQUEST['office']) && $_REQUEST['office']->central)? 'checked="checked"':'');?>  id="central-office" autocomplete="off"/>
    	            			check this box if it is a central office.
                			</label>
                			<?php if (isset($_REQUEST['errors']['central'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['central']);?></p>
                			<?php }?>
                		</div>
        			</div>        		
        		</div>
    		</fieldset>
    		
    		<?php require_once '_form-adress.php';?>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save</button>
    		</div>
    	</form>
    </div>
</section>