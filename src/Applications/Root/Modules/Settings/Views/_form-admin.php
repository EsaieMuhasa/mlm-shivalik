<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Entities\User;
?>
<section class="panel">
    <header class="panel-heading">
    	Register a system admin
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
                			<label class="form-label" for="name-admin">Name <span class="text-danger">*</span></label>
                			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['admin'])? $_REQUEST['admin']->name:'');?>" id="name-admin" class="form-control" placeholder="put here the first name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['name'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['postName'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-admin">Post name <span class="text-danger">*</span></label>
                			<input type="text" name="postName" value="<?php echo htmlspecialchars(isset($_REQUEST['admin'])? $_REQUEST['admin']->postName:'');?>" id="postName-admin" class="form-control" placeholder="put here thre post name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['postName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['postName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['lastName'])? 'has-error':'');?>">
                			<label class="form-label" for="lastName-admin">Last name <span class="text-danger">*</span></label>
                			<input type="text" name="lastName" value="<?php echo htmlspecialchars(isset($_REQUEST['admin'])? $_REQUEST['admin']->lastName:'');?>" id="lastName-admin" class="form-control" placeholder="put here thre last name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['lastName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['lastName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['email'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-admin">Email <span class="text-danger">*</span></label>
                			<input type="text" name="email" value="<?php echo htmlspecialchars(isset($_REQUEST['admin'])? $_REQUEST['admin']->email:'');?>" id="email-admin" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['email'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['email']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['telephone'])? 'has-error':'');?>">
                			<label class="form-label" for="telephone-admin">Telephone <span class="text-danger">*</span></label>
                			<input type="text" name="telephone" value="<?php echo htmlspecialchars(isset($_REQUEST['admin'])? $_REQUEST['admin']->telephone:'');?>" id="telephone-admin" class="form-control" placeholder="" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['telephone'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['telephone']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['kind'])? 'has-error':'');?>">
                			<label class="form-label" for="kind-admin">Kind <span class="text-danger">*</span></label>
                			<select name="kind" class="form-control" id="kind-admin">
                				<option value="<?php echo User::KIND_M; ?>" <?php echo htmlspecialchars(isset($_REQUEST['admin'])? ($_REQUEST['admin']->kind== User::KIND_M? 'selected="selected"':''):(''));?>><?php echo User::KIND_M_TXT; ?></option>
                				<option value="<?php echo User::KIND_W; ?>" <?php echo htmlspecialchars(isset($_REQUEST['admin'])? ($_REQUEST['admin']->kind== User::KIND_W? 'selected="selected"':''):(''));?>><?php echo User::KIND_W_TXT; ?></option>
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
                			<label class="form-label" for="password-admin">Password <span class="text-danger">*</span></label>
                			<input type="password" name="password" id="password-admin" class="form-control" placeholder="put here the default password" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['password'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['password']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="confirmation-admin">Confirmation <span class="text-danger">*</span></label>
                			<input type="password" name="confirmation" id="confirmation-admin" class="form-control" placeholder="here confirmation of default password" autocomplete="off"/>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['photo'])? 'has-error':'');?>">
                			<label class="form-label" for="photo-admin">Photo (prefered size 250x250) <span class="text-danger">*</span></label>
                			<input type="file" name="photo" id="photo-admin" class="form-control"/>
                			<?php if (isset($_REQUEST['errors']['photo'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['photo']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			
        			<div class="col-md-6">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['office'])? 'has-error':'');?>">
                			<label class="form-label" for="localisation-office">Assignment office<span class="text-danger">*</span></label>
                			
                			<select name="office" id="localisation-office" class="form-control" >
                    			<?php  foreach ($_REQUEST[SettingsController::ATT_OFFICES] as $office) : ?>
                    			<option value="<?php echo $office->id; ?>" title="">
                    				<?php echo htmlspecialchars($office->name); ?>
                				</option>
                    			<?php endforeach; ?>
                			</select>
                			<?php if (isset($_REQUEST['errors']['office'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['office']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		
    		<?php require_once '_form-adress.php';?>
    		
    		<div class="text-center">
        		<button class="btn btn-primary">Sava</button>
    		</div>
    	</form>
    </div>
</section>