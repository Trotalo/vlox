<?php
//require_once(dirname($modx->getOption('base_path')) . '/html/kraken/core/components/kraken/controllers/KrakenBlocksController.php');
//require_once($modx->getOption('core_path') . '/components/kraken/controllers/KrakenBlocksController.php');
if ($modx->getOption('monster_dev')) {
  require_once(dirname($modx->getOption('base_path')) . '/html/kraken/core/components/kraken/controllers/KrakenBlocksController.php');
} else {
  require_once($modx->getOption('core_path') . '/components/kraken/controllers/KrakenBlocksController.php');
}

KrakenBlocksController::loadService($modx);

$resId = $_GET['resId'];
$blockId = $_GET['blockId'];

if (empty($resId) || is_null($resId) ||
  empty($blockId) || is_null($blockId)) {
  throw new Exception("Missing params for: getJsonContent(snippetName: $snippetName 
                                resId: $resId, blockId: $blockId)");
}

return $modx->KrakenBlocksController->getResBlockContent($blockId, $resId);