
<?php 
use Applications\Member\Modules\Account\AccountController;
use PHPBackend\Image2D\Mlm\TreeFormatter;

echo "\"tree\":".$_REQUEST[AccountController::ATT_TREE_FORMATTER]->format(TreeFormatter::FORMAT_JSON); 