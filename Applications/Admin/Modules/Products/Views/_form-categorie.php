<?php
?>
<section class="panel">
    <header class="panel-heading">Categorie <?php echo (isset($_GET['id'])? 'edition':'creation') ?> form</header>
    <div class="panel-body">
    	<form role="form" action="" method="POST">
    		<?php if (isset($_REQUEST['result'])) : ?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<strong><?php echo ($_REQUEST['result']);?></strong>
    			<?php if (isset($_REQUEST['errors']['message'])) : ?>
    			<p><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
    			<?php endif;?>
    		</div>
    		<?php endif;?>
    		<fieldset>
    			<legend>Profile</legend>
        		<div class="form-group <?php echo (isset($_REQUEST['errors']['title'])? 'has-error':'');?>">
        			<label class="form-label" for="categorie-title">Title <span class="text-danger">*</span></label>
        			<input type="text" name="title" value="<?php echo htmlspecialchars(isset($_REQUEST['categorie'])? $_REQUEST['categorie']->title:'');?>" id="categorie-title" class="form-control" placeholder="put here categorie title" autocomplete="off"/>
        			<?php if (isset($_REQUEST['errors']['title'])){?>
        			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['title']);?></p>
        			<?php }?>
        		</div>
        		<div class="form-group <?php echo (isset($_REQUEST['errors']['description'])? 'has-error':'');?>">
        			<label class="form-label" for="categorie-description">Short description<span class="text-danger">*</span></label>
        			<textarea rows="5" cols="20"name="description" id="categorie-description" class="form-control" placeholder="put here the short description"><?php echo htmlspecialchars(isset($_REQUEST['categorie'])? ($_REQUEST['categorie']->description):'');?></textarea>
        			<?php if (isset($_REQUEST['errors']['description'])){?>
        			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['description']);?></p>
        			<?php }?>
        		</div>
    		</fieldset>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Save <?php echo (isset($_GET['id'])? 'modification':'') ?></button>
    		</div>
    	</form>
    </div>
</section>