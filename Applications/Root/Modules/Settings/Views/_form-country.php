<section class="panel">
    <header class="panel-heading">
    	Register country
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
    		<div class="row">
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['name'])? 'has-error':'');?>">
            			<label class="form-label" for="name-country">Name <span class="text-danger">*</span></label>
            			<input type="text" name="name" value="<?php echo htmlspecialchars(isset($_REQUEST['country'])? $_REQUEST['country']->name:'');?>" id="name-country" class="form-control" placeholder="put here country name" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['name'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['name']);?></p>
            			<?php }?>
            		</div>
    			</div>
    			<div class="col-md-6">
            		<div class="form-group <?php echo (isset($_REQUEST['errors']['abbreviation'])? 'has-error':'');?>">
            			<label class="form-label" for="abbreviation-country">Abbreviation <span class="text-danger">*</span></label>
            			<input type="text" name="abbreviation" value="<?php echo htmlspecialchars(isset($_REQUEST['country'])? $_REQUEST['country']->abbreviation:'');?>" id="abbreviation-country" class="form-control" placeholder="put here generaion abbreviation" autocomplete="off"/>
            			<?php if (isset($_REQUEST['errors']['abbreviation'])){?>
            			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['abbreviation']);?></p>
            			<?php }?>
            		</div>
    			</div>
    		</div>
    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save</button>
    		</div>
    	</form>
    </div>
</section>