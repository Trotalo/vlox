<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

//$coreLocation = $modx->getOption('vlox.core_path') . ;
$coreLocation = $modx->getOption('vlox.core_path', null,
  $modx->getOption('core_path') . 'components/vlox/');

require_once($coreLocation . 'controllers/VloxController.php');
/*if (Utils::getInstance()->isDevMode()) {
  require_once(dirname($modx->getOption('base_path')) . '/html/vlox/core/components/vlox/controllers/VloxController.php');
} else {
  require_once($modx->getOption('core_path') . '/components/vlox/controllers/VloxController.php');
}*/

VloxController::loadService($modx);

$resId = $_GET['resId'];
$blockId = $_GET['blockId'];

if (empty($resId) || is_null($resId) ||
  empty($blockId) || is_null($blockId)) {
  throw new Exception("Missing params for: getJsonContent(snippetName: $snippetName 
                                resId: $resId, blockId: $blockId)");
}

return $modx->VloxController->getResBlockContent($blockId, $resId);