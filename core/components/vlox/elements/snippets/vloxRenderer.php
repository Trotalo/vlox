<?php
//$coreLocation = $modx->getOption('kraken.core_path') . ;
$coreLocation = $modx->getOption('kraken.core_path', null, $modx->getOption('core_path') . 'components/kraken/');

require_once($coreLocation . 'controllers/VloxController.php');
/*if (Utils::getInstance()->isDevMode()) {
  require_once(dirname($modx->getOption('base_path')) . '/html/kraken/core/components/kraken/controllers/VloxController.php');
} else {
  require_once($modx->getOption('core_path') . '/components/kraken/controllers/VloxController.php');
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