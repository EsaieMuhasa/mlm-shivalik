<?php
use Applications\Admin\Modules\Offices\OfficesController;
?>
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
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['member'])? 'has-error':'');?>">
                			<label class="form-label" for="member-office">ID of the member who owns the office<span class="text-danger">*</span></label>
                			<input type="text" name="member" value="<?php echo htmlspecialchars(isset($_POST['member'])? ($_POST['member']):'');?>" id="member-office" class="form-control" placeholder="put here the ID of member" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['member'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['member']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['size'])? 'has-error':'');?>">
                			<label class="form-label" for="size-office">Office size <span class="text-danger">*</span></label>
                			<select name="size" class="form-control" id="size-office">
	            				<?php foreach ($_REQUEST[OfficesController::ATT_SIZES] as $size) : ?>
	            				<option value="<?php echo $size->id; ?>" <?php echo htmlspecialchars((isset($_REQUEST['officeSize']) && $_REQUEST['officeSize']->size!=null)? ($_REQUEST['officeSize']->size->id==$size->id? 'selected="selected"':''):(''));?>><?php echo htmlspecialchars($size->name); ?></option>
	            				<?php endforeach;?>
	            			</select>
                			<?php if (isset($_REQUEST['errors']['size'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['size']);?></p>
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