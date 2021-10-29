<?php
//require_once(dirname($modx->getOption('base_path')) . '/html/kraken/core/components/kraken/controllers/KrakenBlocksController.php');
//require_once($modx->getOption('core_path') . '/components/kraken/controllers/KrakenBlocksController.php');
if ($modx->getOption('kraken.monster_dev')) {
  require_once(dirname($modx->getOption('base_path')) . '/html/kraken/core/components/kraken/controllers/KrakenBlocksController.php');
} else {
  require_once($modx->getOption('core_path') . '/components/kraken/controllers/KrakenBlocksController.php');
}

KrakenBlocksController::loadService($modx);

$resIdStr = $modx->resource->get('id');
$resId = $resIdStr + 0;


if (empty($resId) || is_null($resId) ) {
  throw new Exception("Missing params for: renderComponentsTag(resId: $resId)");
}
//TODO this must be changed go generate the tags
return $modx->KrakenBlocksController->renderComponentsTag($resId);