<section class="panel">
    <header class="panel-heading">Select element by budget configuration</header>
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

            <?php foreach ($_REQUEST['elements'] as $item) : ?>
                <div class="form-group <?php echo (isset($_REQUEST['errors']['elements'])? 'has-error':'');?>">
                    <label class="checkbox" for="field-rubric-<?php echo $item->id; ?>">
                        <input type="checkbox" name="elements[]" value="<?php echo $item->id;?>" id="field-rubric-<?php echo $item->id; ?>" class="" autocomplete="off"/>
                        <?php echo htmlspecialchars($item->label); ?>
                    </label>
                </div>
            <?php endforeach; ?>
    		    		
    		<div class="text-center">
        		<button class="btn btn-primary"><span class="fa fa-send"></span> Next steep</button>
    		</div>
    	</form>
    </div>
</section>