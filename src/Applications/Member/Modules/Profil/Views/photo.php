
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-user"></i> My profil</h3>
    	<ol class="breadcrumb">
    		<li>
    			<i class="fa fa-user"></i>
    			<a href="/member/profil/">Profil</a>
			</li>
			<li>
    			<i class="fa fa-picture"></i>
    			<span>Photo</span>
			</li>
		</ol>
    </div>
</div>

<section class="panel">
    <header class="panel-heading">
    	<strong>Update your profil picture</strong>
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
    			<legend>Update your photo <span class="text-danger">*</span></legend>
    			<div class="row">
    				<div class="col-md-6">
        				<div class="form-group <?php echo (isset($_REQUEST['errors']['photo'])? 'has-error':'');?>">
                			<label class="form-label" for="member-photo">Your picture</label>
                			<input type="file" name="photo" id="member-photo" class="form-control" autocomplete="off"/>
                			<?php if (isset($_REQUEST['errors']['photo'])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['photo']);?></p>
                			<?php }?>
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