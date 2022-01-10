<?php
use Core\Shivalik\Entities\User;
?>
<section class="panel">
    <header class="panel-heading">
    	Register a office officeAdmin
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
    			<legend>Profil</legend>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error':'');?>">
                			<label class="form-label" for="name-officeAdmin">Name <span class="text-danger">*</span></label>
                			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? $_REQUEST['officeAdmin']->name:'');?>" id="name-officeAdmin" class="form-control" placeholder="put here the first name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['name'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['postName'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-officeAdmin">Post name <span class="text-danger">*</span></label>
                			<input type="text" name="postName" value="<?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? $_REQUEST['officeAdmin']->postName:'');?>" id="postName-officeAdmin" class="form-control" placeholder="put here thre post name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['postName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['postName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['lastName'])? 'has-error':'');?>">
                			<label class="form-label" for="lastName-officeAdmin">Last name <span class="text-danger">*</span></label>
                			<input type="text" name="lastName" value="<?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? $_REQUEST['officeAdmin']->lastName:'');?>" id="lastName-officeAdmin" class="form-control" placeholder="put here thre last name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['lastName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['lastName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['email'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-officeAdmin">Email <span class="text-danger">*</span></label>
                			<input type="text" name="email" value="<?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? $_REQUEST['officeAdmin']->email:'');?>" id="email-officeAdmin" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['email'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['email']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['telephone'])? 'has-error':'');?>">
                			<label class="form-label" for="telephone-officeAdmin">Telephone <span class="text-danger">*</span></label>
                			<input type="text" name="telephone" value="<?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? $_REQUEST['officeAdmin']->telephone:'');?>" id="telephone-officeAdmin" class="form-control" placeholder="" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['telephone'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['telephone']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['kind'])? 'has-error':'');?>">
                			<label class="form-label" for="kind-officeAdmin">Kind <span class="text-danger">*</span></label>
                			<select name="kind" class="form-control" id="kind-officeAdmin">
                				<option value="<?php echo User::KIND_M; ?>" <?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? ($_REQUEST['officeAdmin']->kind== User::KIND_M? 'selected="selected"':''):(''));?>><?php echo User::KIND_M_TXT; ?></option>
                				<option value="<?php echo User::KIND_W; ?>" <?php echo htmlspecialchars(isset($_REQUEST['officeAdmin'])? ($_REQUEST['officeAdmin']->kind== User::KIND_W? 'selected="selected"':''):(''));?>><?php echo User::KIND_W_TXT; ?></option>
                			</select>
                			<?php if (isset($_REQUEST['errors']['kind'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['kind']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
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
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['photo'])? 'has-error':'');?>">
                			<label class="form-label" for="photo-officeAdmin">Photo (prefered size 250x250) <span class="text-danger">*</span></label>
                			<input type="file" name="photo" id="photo-officeAdmin" class="form-control"/>
                			<?php if (isset($_REQUEST['errors']['photo'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['photo']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		
    		<?php require_once '_form-adress.php';?>
    		
    		<div class="text-center">
        		<button class="btn btn-primary">Save</button>
    		</div>
    	</form>
    </div>
</section>