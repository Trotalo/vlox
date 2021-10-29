<?php

require_once(dirname(__DIR__, 4) . '/config.core.php');

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';


$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError', '', '');

if ($modx->getRequest()) {
  $modx->request->sanitizeRequest();
}

$modelLocation = $modx->getOption('kraken.monster_dev') ?
                      MODX_BASE_PATH . 'kraken/core/components/kraken/model/' :
                      MODX_CORE_PATH . 'components/kraken/model/';

//TODO this MUST go away from here to its own controller
if(!$modx->addPackage('kraken', $modelLocation)) {
  $modx->log(xPDO::LOG_LEVEL_ERROR, "krakenBlocks package not found at " . $modelLocation);
  throw new Exception("krakenBlocks package not found at $modelLocation");
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