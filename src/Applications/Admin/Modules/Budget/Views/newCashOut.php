<?php
$max = $_REQUEST['max'];
?>
<section class="panel">
    <header class="panel-heading">Withdrawal</header>
    <div class="panel-body">
        <form role="form" action="" method="POST" enctype="multipart/form-data">
            <?php if (isset($_REQUEST['result'])) : ?>
                <div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
                    <?php echo ($_REQUEST['result']);?>
                    <?php if (isset($_REQUEST['errors']['message'])){?>
                    <hr/><p><?php echo htmlspecialchars($_REQUEST['errors']['message']);?></p>
                    <?php }?>
                </div>
            <?php endif; ?>
            <div class="form-group <?php echo (isset($_REQUEST['errors']['amount'])? 'has-error':'');?>">
                <label class="form-label" for="amount-output">Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" name="amount"  value="<?php echo htmlspecialchars(isset($_REQUEST['output'])? $_REQUEST['output']->amount:'');?>" id="amount-output" class="form-control" autocomplete="off"/>
                    <?php if(!isset($_GET['id'])) :  ?>
                    <span class="input-group-addon">
                        <span class="text-danger"><?php echo "Max: {$max} $"; ?></span>
                    </span>
                    <?php endif; ?>
                </div>
                <?php if (isset($_REQUEST['errors']['amount'])){?>
                <p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']['amount']);?></p>
                <?php }?>
            </div>
            <div class="text-center">
        		<button class="btn btn-primary">
                    <span class="fa fa-save"></span> Save
                </button>
    		</div>
        </form>
    </div>
</section>