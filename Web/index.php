<?php 

use Applications\Index\IndexApplication;

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'autoload.php';

$app = new IndexApplication();
$app->run();