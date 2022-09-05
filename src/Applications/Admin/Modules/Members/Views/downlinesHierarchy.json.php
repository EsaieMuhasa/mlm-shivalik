<?php 

use Applications\Admin\Modules\Members\MembersController;
use PHPBackend\Image2D\Mlm\TreeFormatter;

echo  "\"tree\" :{$_REQUEST[MembersController::ATT_TREE_FORMATER]->format(TreeFormatter::FORMAT_JSON)}";