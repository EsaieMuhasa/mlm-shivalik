<?php 

use Applications\Admin\Modules\Members\MembersController;
use Library\Image2D\Mlm\TreeFormatter;
?>
<div class="row">
	<div class="col-xs-12">
        <?php echo  "{$_REQUEST[MembersController::ATT_TREE_FORMATER]->format(TreeFormatter::FORMAT_HTML)}"; ?>
	</div>
</div>
