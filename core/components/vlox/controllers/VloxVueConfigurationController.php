<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

require_once MODX_BASE_PATH . 'config.core.php';
require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

require_once 'VloxBaseController.php';
require_once 'VloxController.php';

class VloxVueConfigurationController extends  VloxBaseController {

  function __construct() {
    parent::__construct();
    VloxController::loadService($this->modx, 'VloxController');
  }


  public function storeMainJs($fileContents) {
    $chunk = $this->modx->getObject('modChunk', array('name'=>'mainJs'));
    $chunk->set('snippet', $fileContents);
    $chunk->set('description', 1);
    $chunk->save();
  }

  public function getMainJs(){
    $chunk = $this->modx->getObject('modChunk', array('name'=>'mainJs'));
    return $chunk->get('snippet');
  }

  public function addNpmModule($npmModule, $resId) {
    $baseCommand = "npm install %s";
    $currentWorkingDir = getcwd();
    chdir($this->COMPONENTS_ROUTE);
    $this->modx->VloxController->stopServer();
    $npmRespose = shell_exec(sprintf($baseCommand, $npmModule));
    chdir($currentWorkingDir);
    $this->modx->VloxController->launchNodeServer($resId);
    return $npmRespose;
  }

  public function removeNpmModule($npmModule) {
    //shell_exec
  }
}