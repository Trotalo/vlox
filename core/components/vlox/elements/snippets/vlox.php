<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

//$coreLocation = $modx->getOption('vlox.core_path') . 'controllers/VloxController.php';
$coreLocation = $modx->getOption('vlox.core_path', null,
                            $modx->getOption('core_path') . 'components/vlox/');
require_once($coreLocation . 'controllers/VloxController.php');

VloxController::loadService($modx, 'VloxController');

if (!isset($resId) || empty($resId)) {
  $resIdStr = $modx->resource->get('id');
  $resId = $resIdStr + 0;
}

if (empty($resId) || is_null($resId) ) {
  throw new Exception("Missing params for: renderComponentsTag(resId: $resId)");
}

if (empty($isEditingVlox) || is_null($isEditingVlox) ) {
  throw new Exception("Missing params for: renderComponentsTag(isEditingVlox)");
}
//TODO this must be changed go generate the tags
return $modx->VloxController->renderComponentsTag($resId, $isEditingVlox);