<section class="col-lg-6 col-md-6 col-sm-8 col-xs-10 col-lg-offset-3 col-md-offset-3 col-sm-offset-2 col-xs-offset-1 login-container">
    <form action="" method="post">
        <div class="login-form-header">
            <span class="icon">
                <span class="fa fa-user-plus"></span>
            </span>
        </div>
        <div class="alert alert-danger">
        	<p>
        		<span class="fa fa-info-circled"></span>This form is not functional for administrative reasons. Please contact for more information.
        	</p>
        </div>
        <?php if (isset($_REQUEST['result'])) : ?>
    	<div class="alert alert-danger">
    		<strong class="text-danger text-center"><?php echo ($_REQUEST['result']);?></strong>
    		<?php if (isset($_REQUEST['errors']['message'])){?>
    		<p class="text-danger"><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    		<?php }?>
    	</div>
    	<?php endif;?>
        <div class="registrer-form-body">
        	<div class="row">
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="name" type="text" class="form-control input-lg" placeholder="name" autocomplete="off">
                        <?php if (isset($_REQUEST['errors']['name'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['name']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        			<div class="form-group  <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="postName" type="text" class="form-control input-lg" placeholder="post name" autocomplete="off">
                        <?php if (isset($_REQUEST['errors']['postName'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['postName']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        			<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="lastName" type="text" class="form-control input-lg" placeholder="last name" autocomplete="off">
                        <?php if (isset($_REQUEST['errors']['lastName'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['lastName']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        			<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
        				<select class="form-control input-lg" name="kind">
        				</select>
                        <?php if (isset($_REQUEST['errors']['kind'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['kind']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		<div class="col-xs-12">
        			<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <select class="form-control input-lg" name="country">
        				</select>
                        <?php if (isset($_REQUEST['errors']['country'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['country']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        			<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="telephone" type="tel" class="form-control input-lg" placeholder="Telephone" autocomplete="off">
                        <?php if (isset($_REQUEST['errors']['telephone'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['telephone']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        			<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="email" type="text" class="form-control input-lg" placeholder="Email"  autocomplete="off">
                        <?php if (isset($_REQUEST['errors']['email'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['email']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="password" type="password" class="form-control input-lg" placeholder="password" autocomplete="off">
                    	<?php if (isset($_REQUEST['errors']['password'])) { ?>
                    	<p class="text-danger"><?php echo ($_REQUEST['errors']['password']); ?></p>
                    	<?php }?>
                    </div>
        		</div>
        		
        		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                     <div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error' : ''); ?>">
                        <input name="confirmation" type="password" class="form-control input-lg" placeholder="confirm your password" autocomplete="off">
                    </div>
        		</div>
        	</div>

            <div class="form-group">
                <div class="alert alert-info">
                	<strong>
                		<a href="/license.html">
                			<span class="fa fa-book"></span>
                			License Agreement
            			</a>
            		</strong>
            		<br/>By clicking on the registration button, you accept the license agreement described on <a href="/license.html">this page</a>
                </div>
            </div>
            
            <div class="login-form-footer">
                <button type="submit" class="btn btn-primary">
                    <span class="fa fa-send"></span> Subscribe
                </button>
            </div>
            
            <div class="form-group" style="padding-top: 15px;">
                <div class="alert alert-warning">
                	<strong>
                		<a href="/login.html" class="text-warning"><span class="fa fa-login"></span> Login</a>
                	</strong>
        			<p>Already have an account? click <a href="/login.html"><span class="fa fa-right-hand"></span> here</a> to login to your account</p>
                </div>
            </div>
        </div>
    </form>
</section>
