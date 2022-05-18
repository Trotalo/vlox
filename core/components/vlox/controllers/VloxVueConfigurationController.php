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

  public function modifyNpmModule($npmModule, $resId, $operation) {
    $baseCommand = $operation === 0 ? "npm install %s" : "npm uninstall %s";
    $currentWorkingDir = getcwd();
    chdir($this->COMPONENTS_ROUTE);
    $this->modx->VloxController->stopServer();
    $npmRespose = shell_exec(sprintf($baseCommand, $npmModule));
    chdir($currentWorkingDir);
    $this->modx->VloxController->launchNodeServer($resId);
    return $npmRespose;
  }

  public function buildResource($resId){
    $cmd = "npm --prefix $this->COMPONENTS_ROUTE run build:$resId";
    $npmRespose = shell_exec($cmd);
    if( is_null($npmRespose)) {
      throw new Exception("The command $cmd failed! check your server logs");
    }
    //once the process finished succesfully, we must copy the html to the resource and the resources
    $renderedIndex = file_get_contents($this->COMPONENTS_ROUTE . $resId . '/dist/index.html');
    if ($resource = $this->modx->getObject('modResource',$resId)){
      $resource->setContent($renderedIndex);
      if (!$resource->save()){
        throw new Exception('There was an error saving Resource '.$resource->get('pagetitle'));
      }
    }
    else{
      throw new Exception('Resource '.$resId.'not found');
    }
    //once the resource its updated copy the asset folders
    $localAssets = $this->modx->getOption('assets_path');
    if(is_dir($localAssets . '/' + $resId)) {
      if(!$this->delTree($localAssets . '/' + $resId)) {
        throw new Exception('Failed to remove: ' . $localAssets . '/' + $resId);
      }
    }
    //if we could delete the assets folder or it didnt exists, copy the generated resources
    $cmd = sprintf("cp -r %s %s",
      $this->COMPONENTS_ROUTE . $resId . '/dist/assets/' . $resId,
      $localAssets . $resId);
    $copyResult = shell_exec($cmd);
    if (is_null($copyResult) || empty($copyResult)) {
      throw new Exception("Failed executing: $cmd");
    }
    return $npmRespose;
  }

  public function isNpmInstalled() {
    $response = is_dir($this->COMPONENTS_ROUTE . 'node_modules');
    return $response;
  }

  public function installNpm() {
    $currentWorkingDir = getcwd();
    chdir($this->COMPONENTS_ROUTE);
    $cmd = "npm install";
    $npmRespose = shell_exec($cmd);
    chdir($currentWorkingDir);
    if( is_null($npmRespose)) {
      var_dump(shell_exec("$cmd 2>&1"));
      $debug = var_export(shell_exec("$cmd 2>&1"), true);
      throw new Exception("The command $cmd failed, error: $debug");
    }
    return $npmRespose;
  }

  /**
   * Taken from https://www.php.net/manual/en/function.rmdir.php
   * @param $dir
   * @return bool
   */

  private function delTree($dir) {
    if(!isset($dir) || is_null($dir)) {
      return false;
    }
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }

}