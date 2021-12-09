<?php 

use Applications\Admin\Modules\Members\MembersController;
use Library\Image2D\Mlm\TreeFormatter;

echo  "\"tree\" :{$_REQUEST[MembersController::ATT_TREE_FORMATER]->format(TreeFormatter::FORMAT_JSON)}";