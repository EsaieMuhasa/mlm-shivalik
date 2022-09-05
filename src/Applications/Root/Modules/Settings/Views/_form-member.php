<?php
use Applications\Root\Modules\Settings\SettingsController;
use Core\Shivalik\Entities\User;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<section class="panel">
    <header class="panel-heading">
    	Register a system member
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-danger">
    			<?php echo ($_REQUEST['result']);?>
    			<?php if (isset($_REQUEST['errors']['message'])){?>
    			<hr/><p><?php echo ($_REQUEST['errors']['message']);?></p>
    			<?php }?>
    			<?php if (isset($_REQUEST['errors']['foot'])){?>
    			<p><?php echo ($_REQUEST['errors']['foot']);?></p>
    			<?php }?>
    		</div>
    		<?php }?>
    		<fieldset>
    			<legend>Tree metadata</legend>
    			<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['child'])? 'has-error':'');?>">
                			<label class="form-label" for="member-child">Child (put in this filed ID of member who will be below the account level)</label>
                			<input type="text" name="child" value="<?php echo htmlspecialchars(isset($_POST['child'])? $_POST['child'] :'');?>" id="member-child" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['child'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['child']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
    		</fieldset>
    		<fieldset>
    			<legend>Member's rank requested<span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-12">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['grade'])? 'has-error':'');?>">                			
                			<div class="row">
                    			<?php  foreach ($_REQUEST[SettingsController::ATT_GRADES] as $grade) : ?>
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
                    					$membership = 20;
                    					$officePart = 10;
                    					$product = $grade->amount - $membership - $officePart;
                    					?>
                    					
                    					<span style="display: block;">
                        					<span class="label label-default">
                        						<?php echo ("Office: {$officePart} {$config->get('devise')}"); ?>
                        					</span>
                    					</span>
					
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
    		<fieldset>
    			<legend>Member profile</legend>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error':'');?>">
                			<label class="form-label" for="name-member">Name <span class="text-danger">*</span></label>
                			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->name:'');?>" id="name-member" class="form-control" placeholder="put here the first name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['name'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['postName'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-member">Post name <span class="text-danger">*</span></label>
                			<input type="text" name="postName" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->postName:'');?>" id="postName-member" class="form-control" placeholder="put here thre post name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['postName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['postName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['lastName'])? 'has-error':'');?>">
                			<label class="form-label" for="lastName-member">Last name <span class="text-danger">*</span></label>
                			<input type="text" name="lastName" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->lastName:'');?>" id="lastName-member" class="form-control" placeholder="put here thre last name" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['lastName'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['lastName']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['email'])? 'has-error':'');?>">
                			<label class="form-label" for="postName-member">Email </label>
                			<input type="text" name="email" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->email:'');?>" id="email-member" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['email'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['email']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['telephone'])? 'has-error':'');?>">
                			<label class="form-label" for="telephone-member">Telephone <span class="text-danger">*</span></label>
                			<input type="text" name="telephone" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->telephone:'');?>" id="telephone-member" class="form-control" placeholder="" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['telephone'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['telephone']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['kind'])? 'has-error':'');?>">
                			<label class="form-label" for="kind-member">Kind <span class="text-danger">*</span></label>
                			<select name="kind" class="form-control" id="kind-member">
                				<option value="<?php echo User::KIND_M; ?>" <?php echo htmlspecialchars(isset($_REQUEST['member'])? ($_REQUEST['member']->kind== User::KIND_M? 'selected="selected"':''):(''));?>><?php echo User::KIND_M_TXT; ?></option>
                				<option value="<?php echo User::KIND_W; ?>" <?php echo htmlspecialchars(isset($_REQUEST['member'])? ($_REQUEST['member']->kind== User::KIND_W? 'selected="selected"':''):(''));?>><?php echo User::KIND_W_TXT; ?></option>
                			</select>
                			<?php if (isset($_REQUEST['errors']['kind'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['kind']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['photo'])? 'has-error':'');?>">
                			<label class="form-label" for="photo-member">Photo (prefered size 250x250) <span class="text-danger">*</span></label>
                			<input type="file" name="photo" id="photo-member" class="form-control"/>
                			<?php if (isset($_REQUEST['errors']['photo'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['photo']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['pseudo'])? 'has-error':'');?>">
                			<label class="form-label" for="username-member">Username <span class="text-danger">*</span></label>
                			<input type="text" name="pseudo" id="username-member" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->pseudo:'');?>" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['pseudo'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['pseudo']);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="password-member">Password <span class="text-danger">*</span></label>
                			<input type="password" name="password" id="password-member" class="form-control" placeholder="put here the default password" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['password'])){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['password']);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST['errors']['password'])? 'has-error':'');?>">
                			<label class="form-label" for="confirmation-member">Confirmation <span class="text-danger">*</span></label>
                			<input type="password" name="confirmation" id="confirmation-member" class="form-control" placeholder="here confirmation of default password" autocomplete="off"/>
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