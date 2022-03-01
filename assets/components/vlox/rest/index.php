<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

require_once(dirname(__DIR__, 4) . '/config.core.php');

include_once MODX_CORE_PATH . 'model/modx/modx.class.php';


$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError', '', '');

if ($modx->getRequest()) {
  $modx->request->sanitizeRequest();
}

//$coreLocation = $modx->getOption('vlox.core_path') . 'model/';
$coreLocation = $modx->getOption('vlox.core_path', null, $modx->getOption('core_path')
                                                                                    . 'components/vlox/') . 'model/';

//TODO this MUST go away from here to its own controller
if(!$modx->addPackage('vlox', $coreLocation)) {
  $modx->log(xPDO::LOG_LEVEL_ERROR, "vloxBlockss package not found at " . $coreLocation);
  throw new Exception("vloxBlockss package not found at $coreLocation");
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