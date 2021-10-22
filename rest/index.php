<?php

require_once(dirname(__DIR__, 1) . '/modelConfig/build/mysqlTable.php');


$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError', '', '');

if ($modx->getRequest()) {
  $modx->request->sanitizeRequest();
}

//TODO this MUST go away from here to its own controller
if(!$modx->addPackage('krakenBlocks', MODX_CORE_PATH . 'components/krakenBlocks/model/')) {
  $this->modx->log(xPDO::LOG_LEVEL_ERROR, "krakenBlocks package not found");
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