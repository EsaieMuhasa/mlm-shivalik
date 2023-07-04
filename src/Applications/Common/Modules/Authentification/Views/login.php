
<section class="col-lg-4 col-md-4 col-sm-6 col-xs-10 col-lg-offset-4 col-md-offset-4 col-sm-offset-3 col-xs-offset-1 login-container">
    <form action="" method="post">
        <div class="login-form-header">
            <span class="icon">
                <span class="fa fa-lock"></span>
            </span>
        </div>
        <?php if (isset($_REQUEST['result'])) : ?>
    	<div class="alert alert-danger">
    		<strong class="text-danger text-center"><?php echo ($_REQUEST['result']);?></strong>
    		<?php if (isset($_REQUEST['errors']['message'])){?>
    		<p class="text-danger"><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    		<?php }?>
    	</div>
    	<?php endif;?>
        <div class="login-form-body">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <span class="fa fa-user"></span>
                    </span>
                    <input name="pseudo" type="text" value="<?php echo isset($_POST['pseudo'])? $_POST['pseudo'] : ''; ?>" class="form-control input-lg" placeholder="username" aria-describedby="basic-addon1">
                </div>
                <?php if (isset($_REQUEST['errors']['pseudo'])) { ?>
            	<p class="text-danger"><?php echo ($_REQUEST['errors']['pseudo']); ?></p>
            	<?php }?>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon2">
                        <span class="fa fa-key"></span>
                    </span>
                    <input name="password" type="password" class="form-control input-lg" placeholder="password" aria-describedby="basic-addon2">
                </div>
            	<?php if (isset($_REQUEST['errors']['password'])) { ?>
            	<p class="text-danger"><?php echo ($_REQUEST['errors']['password']); ?></p>
            	<?php }?>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember" id=""> Remember my connection 
                    </label>
                </div>
            </div>
        </div>
        <div class="login-form-footer">
        	<p class="text-right">
        		<a href="">Forgot your password?</a>
        	</p>
            <button type="submit" class="btn btn-primary">
                <span class="fa fa-login"></span> Login
            </button>
            
            <div class="form-group" style="padding-top: 15px;">
                <div class="alert alert-info">
                	<strong>
                		<a href="/inscription.html"><span class="fa fa-user-plus"></span> Subcribe now</a>
                	</strong>
                	<p>You don't have an account? click <a href="/inscription.html"><span class="fa fa-right-hand"></span> here</a> to create an account now</p>
                </div>
            </div>

        </div>
    </form>
</section>






