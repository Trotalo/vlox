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

  //TODO ajustar para hacer el build basado en el parametro del proyecto, si no hay, el build tal como esta
  //si esta, debe mandarlo es a la carpeta del otro extra
  public function buildResource($resId, $deploy = true){
    $cmd = "npm --prefix $this->COMPONENTS_ROUTE run build";
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
    $localAssets = '';
    $assetsOrigin = '';
    $currentProject = $this->modx->getOption('vlox.project');
    $defaultAssets = '';
    $folderToDelete = '';
    //TODO pending to modify to make it both available rigth rigth away and inside the external project
    if ($deploy && !empty($currentProject)) {
      $baseAssets = $this->modx->getOption('assets_path');
      $baseAssets = substr($baseAssets, 0 , strlen($baseAssets) - 1);
      $last_slash = strrpos($baseAssets, '/');
      $localAssets = substr($baseAssets, 0, $last_slash)  . "/$currentProject/assets/components/$currentProject/";
      $assetsOrigin = $this->COMPONENTS_ROUTE . $resId . "/dist/assets/components/$currentProject/$resId";
      $defaultAssets = $this->modx->getOption('assets_path') . "components/$currentProject/";

      //in this section we copy to the default modx assets location to have it working locally
      $folderToDelete = $defaultAssets . $resId;
      $this->modx->log(xPDO::LOG_LEVEL_WARN, "Deleting: $folderToDelete is dir: " . is_dir($folderToDelete));
      if(is_dir($folderToDelete)) {
        if(!$this->delTree($folderToDelete)) {
          throw new Exception('Failed to remove: ' . $localAssets . '/' . $resId);
        }
      }
      //if we could delete the assets folder or it didnt exists, copy the generated resources

      $this->recurseCopy($assetsOrigin, $defaultAssets . $resId, '');

      //Copy the images
      /*$this->recurseCopy($this->COMPONENTS_ROUTE . $resId . '/dist/img',
        $defaultAssets . '/img', '');*/
    }else {
      $localAssets = $this->modx->getOption('assets_path');
      $assetsOrigin = $this->COMPONENTS_ROUTE . $resId . '/dist/assets/' . $resId;
    }

    $folderToDelete = $localAssets . $resId;
    $this->modx->log(xPDO::LOG_LEVEL_WARN, "Deleting: $folderToDelete is dir: " . is_dir($folderToDelete));
    if(is_dir($folderToDelete)) {
      if(!$this->delTree($folderToDelete)) {
        throw new Exception('Failed to remove: ' . $localAssets . '/' . $resId);
      }
    }
    //if we could delete the assets folder or it didnt exists, copy the generated resources

    $this->recurseCopy($assetsOrigin, $localAssets . $resId, '');

    //Copy the images
    $this->recurseCopy($this->COMPONENTS_ROUTE . $resId . '/dist/img',
      $this->basePath . '/img', '');

    //finally if

    $this->modx->log(xPDO::LOG_LEVEL_INFO, "Copied elements $npmRespose");
    //finally, create the zip and return it to the client for downloading
    $pageName = $resource->get('pagetitle');
    $zipLocation = $localAssets . $resId . '/' . $pageName . '.zip';
    $this->modx->log(xPDO::LOG_LEVEL_WARN, "trying with: $zipLocation");
    //fir we check if the zip exists, and if it does, we delete it
    if (file_exists($zipLocation)) {
      unlink($zipLocation);
    }
    $zip = new ZipArchive();
    if ($zip->open($zipLocation, ZipArchive::CREATE)!==TRUE) {
      exit("cannot open <$this->COMPONENTS_ROUTE . $resId . '/dist'>\n");
    }
    $this->zipFolder($this->COMPONENTS_ROUTE . $resId . '/dist', $zip);
    $zip->close();
    return str_replace('/var/www/html/', '', $zipLocation);
  }

  public function isNpmInstalled() {
    $response = is_dir($this->COMPONENTS_ROUTE . 'node_modules');
    return $response;
  }

  public function installNpm() {
    $currentWorkingDir = getcwd();
    chdir($this->COMPONENTS_ROUTE);
    $this->modx->log(MODX_LOG_LEVEL_ERROR, "About to install npm at: $this->COMPONENTS_ROUTE");
    $cmd = "npm install";
    $npmRespose = shell_exec($cmd);
    $this->modx->log(MODX_LOG_LEVEL_ERROR, "Install response was: $npmRespose");
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

  private function zipFolder(string $dir, ZipArchive $zip, string $parentFolder = "") {
    if(!isset($dir) || is_null($dir)) {
      return false;
    }
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      $tmpParent = (!empty($parentFolder) ? "$parentFolder/" : "");
      if (is_dir("$dir/$file")) {
        $zip->addEmptyDir( $tmpParent . $file);
        $this->zipFolder("$dir/$file/", $zip, $tmpParent . $file);
      } else {
        $zip->addFile("$dir/$file", $tmpParent . $file);
      }
      //(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
    }
    return true;

  }
  /**
   * taken from
   * @param string $sourceDirectory
   * @param string $destinationDirectory
   * @param string $childFolder
   */
  private function recurseCopy(
    string $sourceDirectory,
    string $destinationDirectory,
    string $childFolder = ''
  ): void {
    $directory = opendir($sourceDirectory);
    if (!$directory)
      return;

    if (is_dir($destinationDirectory) === false) {
      mkdir($destinationDirectory, 0777, true);
    }

    if ($childFolder !== '') {
      if (is_dir("$destinationDirectory/$childFolder") === false) {
        mkdir("$destinationDirectory/$childFolder");
      }

      while (($file = readdir($directory)) !== false) {
        if ($file === '.' || $file === '..') {
          continue;
        }

        if (is_dir("$sourceDirectory/$file") === true) {
          $this->recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
        } else {
          copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
        }
      }

      closedir($directory);

      return;
    }

    while (($file = readdir($directory)) !== false) {
      if ($file === '.' || $file === '..') {
        continue;
      }

      if (is_dir("$sourceDirectory/$file") === true) {
        $this->recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
      }
      else {
        copy("$sourceDirectory/$file", "$destinationDirectory/$file");
      }
    }

    closedir($directory);
  }

}