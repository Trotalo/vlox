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

$isMODX3 = $modx->getVersionData()['version'] >= 3;
$classPrefix = $isMODX3 ? 'MODX\Revolution\\' : '';

$modx->initialize('web');
if ($modx->services->has('error')) {
  $service = $modx->services->get('error');
}
//$modx->getService('error', 'error.modError', '', '');

if ($modx->getRequest()) {
  $modx->request->sanitizeRequest();
}

//$coreLocation = $modx->getOption('vlox.core_path') . 'model/';
$coreLocation = $modx->getOption('vlox.core_path', null, $modx->getOption('core_path')
                                                                                    . 'components/vlox/') . 'src/Model/';

//TODO this MUST go away from here to its own controller
if(!$modx->addPackage('Vlox', $coreLocation)) {
  $modx->log(xPDO::LOG_LEVEL_ERROR, "vloxBlockss package not found at " . $coreLocation);
  throw new Exception("vloxBlockss package not found at $coreLocation");
}

if (!$modx->services->has('rest.modRestService')) {
  //$service = $modx->services->get('rest.modRestService');
  $modx->services->add('rest.modRestService', function($c) use ($modx) {
    return new \MODX\Revolution\Rest\modRestService($modx, array(
      'basePath' => dirname(__FILE__) . '/controllers/',
      'controllerClassSeparator' => '',
      'controllerClassPrefix' => 'Vlox',
      'xmlRootNode' => 'response',
    ));
  });
}
/*$rest = $modx->getService('rest', 'rest.modRestService', '', array(
  'basePath' => dirname(__FILE__) . '/controllers/',
  'controllerClassSeparator' => '',
  'controllerClassPrefix' => 'Kraken',
  'xmlRootNode' => 'response',
));*/

$rest = $modx->services->get('rest.modRestService');
$rest->prepare();

if (!$rest->checkPermissions()) {
  $rest->sendUnauthorized(true);
}

$rest->process();