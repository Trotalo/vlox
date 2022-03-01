<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

//require_once(dirname($modx->getOption('base_path')) . '/html/vlox/core/components/vlox/controllers/VloxController.php');
//require_once($modx->getOption('core_path') . '/components/vlox/controllers/VloxController.php');
//$coreLocation = $modx->getOption('vlox.core_path');
$coreLocation = $modx->getOption('vlox.core_path', null,
                          $modx->getOption('core_path') . 'components/vlox/');

require_once($coreLocation . 'controllers/VloxController.php');

VloxController::loadService($modx);

$resIdStr = $modx->resource->get('id');
$resId = $resIdStr + 0;


if (empty($resId) || is_null($resId) ) {
  throw new Exception("Missing params for: renderComponentImports(resId: $resId)");
}
//TODO this must be changed go generate the tags
return $modx->VloxController->renderComponentImports($resId);