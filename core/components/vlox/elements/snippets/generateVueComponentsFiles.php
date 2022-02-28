<?php
//$coreLocation = $modx->getOption('vlox.core_path');
$coreLocation = $modx->getOption('vlox.core_path', null,
                          $modx->getOption('core_path') . 'components/vlox/');

require_once($coreLocation . 'controllers/VloxController.php');

VloxController::loadService($modx);

$resIdStr = $modx->resource->get('id');
$resId = $resIdStr + 0;


if (empty($resId) || is_null($resId) ) {
  throw new Exception("Missing params for: getResourcesContent(resId: $resId)");
}
//TODO this must be changed go generate the tags
return $modx->VloxController->generateVueComponentsFiles($resId);