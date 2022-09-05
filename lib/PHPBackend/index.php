<?php 
require_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use PHPBackend\Config\AppManagerConfig;
use PHPBackend\Http\HTTPApplication;

$config = AppManagerConfig::getInstance();

$metadata = $config->findAppMetadata($_SERVER['REQUEST_URI']);
$app = new HTTPApplication($metadata->getName(), $metadata->getNamespace(), $config->getContainer());
$app->run();