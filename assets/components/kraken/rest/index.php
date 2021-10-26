<?php

define('MODX_BASE_PATH', dirname(__FILE__, 7) . '/html/');
define('MODX_CORE_PATH', dirname(__FILE__, 7) . '/coreM0dXF1L3s/');

define('KRAKEN_PATH', MODX_BASE_PATH . 'modxMonster');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

define('MODX_BASE_URL','/modx/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';


$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError', '', '');

if ($modx->getRequest()) {
  $modx->request->sanitizeRequest();
}

//TODO this MUST go away from here to its own controller
//if(!$modx->addPackage('kraken', MODX_CORE_PATH . 'components/krakenBlocks/model/')) {
if(!$modx->addPackage('kraken', MODX_BASE_PATH . 'kraken/core/components/kraken/model/')) {
  $modx->log(xPDO::LOG_LEVEL_ERROR, "krakenBlocks package not found at " . MODX_CORE_PATH . 'components/krakenBlocks/model/');
  throw new Exception("krakenBlocks package not found");
}

$rest = $modx->getService('rest', 'rest.modRestService', '', array(
  'basePath' => dirname(__FILE__) . '/controllers/',
  'controllerClassSeparator' => '',
  'controllerClassPrefix' => 'Kraken',
  'xmlRootNode' => 'response',
));

$rest->prepare();

if (!$rest->checkPermissions()) {
  $rest->sendUnauthorized(true);
}

$rest->process();