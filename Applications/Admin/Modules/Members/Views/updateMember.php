
<?php
use Entities\User;
use Validators\MemberFormValidator;
?>
<section class="panel">
    <header class="panel-heading">
    	Update member profil
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST" enctype="multipart/form-data">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
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
    			<legend>Member profile</legend>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorName)? 'has-error':'');?>">
                			<label class="form-label" for="name-member">Name <span class="text-danger">*</span></label>
                			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->name:'');?>" id="name-member" class="form-control" placeholder="put here the first name" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorName)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorName);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPostName)? 'has-error':'');?>">
                			<label class="form-label" for="postName-member">Post name <span class="text-danger">*</span></label>
                			<input type="text" name="postName" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->postName:'');?>" id="postName-member" class="form-control" placeholder="put here thre post name" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPostName)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPostName);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorLastName)? 'has-error':'');?>">
                			<label class="form-label" for="lastName-member">Last name <span class="text-danger">*</span></label>
                			<input type="text" name="lastName" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->lastName:'');?>" id="lastName-member" class="form-control" placeholder="put here thre last name" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorLastName)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorLastName);?></p>
                			<?php }?>
                		</div>
        			</div>
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorEmail)? 'has-error':'');?>">
                			<label class="form-label" for="postName-member">Email </label>
                			<input type="text" name="email" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->email:'');?>" id="email-member" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorEmail)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorEmail);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		<div class="row">
        			<div class="col-md-6">
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorTelephone)? 'has-error':'');?>">
                			<label class="form-label" for="telephone-member">Telephone <span class="text-danger">*</span></label>
                			<input type="text" name="telephone" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->telephone:'');?>" id="telephone-member" class="form-control" placeholder="" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorTelephone)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorTelephone);?></p>
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
                		<div class="form-group <?php echo (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPseudo)? 'has-error':'');?>">
                			<label class="form-label" for="username-member">Username <span class="text-danger">*</span></label>
                			<input type="text" name="pseudo" id="username-member" class="form-control" value="<?php echo htmlspecialchars(isset($_REQUEST['member'])? $_REQUEST['member']->pseudo:'');?>" autocomplete="off"/>
                			<?php if (isset($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPseudo)){?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST[MemberFormValidator::MEMBER_FEEDBACK]->errorPseudo);?></p>
                			<?php }?>
                		</div>
        			</div>
        		</div>
        		
    		</fieldset>
    		
    		<div class="text-center">
        		<button class="btn btn-primary">Save</button>
    		</div>
    	</form>
    </div>
</section>