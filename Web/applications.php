<?php 

use Applications\Index\IndexApplication;
use Applications\Root\RootApplication;
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'autoload.php';
$subDomain =  $_GET['subdomain'];
switch ($subDomain) {    
    case 'admin' : {
        $app = new AdminApplication();
    }break;
    case 'office' : {
    	$app = new OfficeApplication();
    }break;
    case 'member' : {        
        $app = new MemberApplication();
    }break;
    case 'root' : {
        $app = new RootApplication();
    }break;
    
    default:
        $app = new IndexApplication();
    break;
}

$app->run();