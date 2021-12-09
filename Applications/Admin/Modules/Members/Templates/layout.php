<?php
use Library\Page;
use Applications\Admin\Modules\Members\MembersController;

if (isset($_REQUEST[MembersController::ATT_MEMBER])) {
    require_once 'nav.php';
}
echo $_REQUEST[Page::ATT_VIEW]; 
