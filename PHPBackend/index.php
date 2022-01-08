<?php 

use PHPBackend\Config\AppManagerConfig;
use PHPBackend\Http\HTTPApplication;

require_once 'autoload.php';
$config = AppManagerConfig::getInstance();

$metadata = $config->findAppMetadata($_SERVER['REQUEST_URI']);
$app = new HTTPApplication($metadata->getName(), $config->getContainer());
$app->run();