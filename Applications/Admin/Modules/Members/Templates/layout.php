<?php
use Applications\Admin\Modules\Members\MembersController;
use PHPBackend\Page;

if (isset($_REQUEST[MembersController::ATT_MEMBER])) {
    require_once 'nav.php';
}
echo $_REQUEST[Page::ATT_VIEW]; 
