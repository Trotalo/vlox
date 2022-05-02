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



/**
 * Loads the json from vlox_resource_content table
 */
class VloxController extends  VloxBaseController{



  /**
   * @param $chunkName string
   * @param $blockId int
   * @param $resId int
   * @return string
   */
  public function getResBlockContent($blockId, $resId) {
    $jsonObject = array();

    $query = $this->modx->query(" 
      select blocks.chunkName, resourceContent.*
      from modx_vlox_blocks as blocks, modx_vlox_resource_content as resourceContent
      where blocks.id = resourceContent.blockId
      and resourceContent.resourceId = $resId
      and resourceContent.blockId = $blockId 
      order by resourceContent.position");

    if (is_null($query)) {
      throw new Exception("Block content not found for 
                        getResourcesContent(resourceId: $resId)");
    }

    $row = $query->fetch();
    //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
    $chunkName = $row['chunkName'];
    $jsonProps = $row['properties'];

    return $this->buildJsonContent($chunkName, $jsonProps, $row['id']);
  }


  public function buildJsonContent($chunkName, $properties, $componentName) {
    $jsonObject = array();

    $jsonProps = !is_array($properties) ? json_decode($properties, true) : $properties;
    foreach ($jsonProps['items'] as $blockContents) {
      if ($blockContents['name']) {
        $jsonObject[$blockContents['name']] = $blockContents['content'];
      }
    }

    $dynamicPlaceholder = json_encode($jsonObject);

    return $this->modx->getChunk($chunkName, [
      'blockContent' => $dynamicPlaceholder,
      'componentName' => $componentName
    ]);
  }

  /**
   * @param $resId int
   * @return string
   */
  public function generateVueComponentsFiles($resId) {
    $query = $this->loadResourceBlocks($resId);

    if (!file_exists($this->COMPONENTS_ROUTE . $resId)) {
      $this->recurseCopy($this->COMPONENTS_ROUTE . 'sample',
        $this->COMPONENTS_ROUTE . $resId);
    }
    $hasComponents = false;
    $parser = $this->modx->getParser();
    $maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);

    while ($row = $query->fetch()) {
      //remove folder contents the first time we fetch files
      if (!$hasComponents) {
        $files = glob($this->COMPONENTS_ROUTE . $resId . '/src/components/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file)) {
            unlink($file); // delete file
          }
        }
      }
      $hasComponents = true;
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      $compName = strtolower($chunkName . '_' . $resBlockId);
      $blockContent = $this->buildJsonContent($chunkName, $row['properties'], $compName);
      if (empty($blockContent)) {
        throw new Exception($chunkName . ' snippet not found, check your manager configuration!');
      }
      // Parse cached tags, while leaving unprocessed tags in place
      $parser->processElementTags('', $blockContent, false, false, '[[', ']]', [], $maxIterations);
      // Parse uncached tags and remove anything that could not be processed
      $parser->processElementTags('', $blockContent, true, true, '[[', ']]', [], $maxIterations);
      //$document->querySelector("style")->textContent = $compiledStyle;
      $finalBlock = $blockContent;
      //And finally we save the partial vue component
      //first check if the project dir exists

      $vueFileName = $this->COMPONENTS_ROUTE . $resId . '/src/components/' . $compName . '.vue';
      $vueFile = fopen($vueFileName, "w");
      if (!$vueFile) {
        $lastError = error_get_last();
        throw new Exception("Problems creating $vueFileName error was: $lastError");
      }
      fwrite($vueFile, $finalBlock);
      fclose($vueFile);

    }
    if ($hasComponents) {
      //here we should recreate the App.vue file
      $output = $this->modx->getChunk('defaultApp', array('resId'=> $resId));
      $parser->processElementTags('', $output, false, false, '[[', ']]', [], $maxIterations);
      // Parse uncached tags and remove anything that could not be processed
      $parser->processElementTags('', $output, true, true, '[[', ']]', [], $maxIterations);
      //echo $output;
      $vueFileName = $this->COMPONENTS_ROUTE . $resId . '/src/App.vue';
      $vueFile = fopen($vueFileName, "w");
      if (!$vueFile) {
        $lastError = error_get_last();
        throw new Exception("Problems creating $vueFileName error was: $lastError");
      }
      fwrite($vueFile, $output);
      fclose($vueFile);

    }
    /*if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }*/
    //return '';
  }

  public function renderComponentDef($resId) {
    $returnValue = "components: { ";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      //$compName = strtolower(str_replace('.vue', '-' . $resBlockId, $chunkName));\
      $compName = strtolower($chunkName . '_' . $resBlockId);
      //$fileName = str_replace('.vue', $resBlockId . '.vue', $chunkName);
      //$fileName = $compName . '.vue';
      $returnValue .= "$compName,\n";
    }
    $returnValue .= "}";
    if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }
    return $returnValue;
  }

  public function renderComponentImports($resId) {
    $returnValue = "";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      //$compName = strtolower(str_replace('.vue', '-' . $resBlockId, $chunkName));\
      $compName = strtolower($chunkName . '_' . $resBlockId);
      //$fileName = str_replace('.vue', $resBlockId . '.vue', $chunkName);
      $fileName = $compName;
      $returnValue .= "import $compName from './components/$compName';\n";
    }
    return $returnValue;
  }

  public function renderComponentsTag($resId) {
    $returnValue = "";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      $compName = strtolower($chunkName . '_' . $resBlockId);
      $scrollDiv = '<div id="' . $row['id'] . '_' . $row['title'] . '">';
      $returnValue .= $scrollDiv;
      //$returnValue .= "<$compName v-on:toggle-loading=\"toggleLoading\" v-on:show-error=\"toggleError\" ></$compName>";
      $returnValue .= "<$compName></$compName>";
      $returnValue .= "</div>";
    }
    if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }
    return $returnValue;
  }

  public function updatePackage($resId) {
    $packageFileLocation = $this->COMPONENTS_ROUTE . 'package.json';
    $jsonString = file_get_contents($packageFileLocation);
    $data = json_decode($jsonString, true);
    $data["scripts"] = array();
    $data["scripts"]["serve:$resId"] = "env APP_TYPE=$resId vue-cli-service serve $resId/src/main.js";
    $data["scripts"]["build:$resId"] = "env APP_TYPE=$resId vue-cli-service build $resId/src/main.js";
    /*array_push($data["scripts"], ("serve:$resId" => "env APP_TYPE=$resId vue-cli-service serve $resId/src/main.js"));
    array_push($data["scripts"], array( "build:$resId" => "env APP_TYPE=$resId vue-cli-service build $resId/src/main.js"));*/
    $newJsonString = json_encode($data);
    //check if the file can be writen is_writable
    if (is_writable($packageFileLocation)) {
      file_put_contents($packageFileLocation, $newJsonString);
    } else {
      throw new Exception('Wrong permissions for: ' + $packageFileLocation);
    }
  }

  public function getNpmModules() {
    $packageFileLocation = $this->COMPONENTS_ROUTE . 'package.json';
    $jsonString = file_get_contents($packageFileLocation);
    $data = json_decode($jsonString, true);
    return $data['dependencies'];
  }

  public function getNpmLog() {
    $outputFile = $this->COMPONENTS_ROUTE . 'npmOutPut';
    $npmStatus = file_get_contents($outputFile);
    return $npmStatus;
  }

  public function getNpmStatus($resId){
    $pidfile = $this->COMPONENTS_ROUTE . 'pidFile';
    if (file_exists($pidfile)) {
      $resIdFile = $this->COMPONENTS_ROUTE . 'resId';
      $pid = file_get_contents($pidfile);
      $tmpResId = intval(file_get_contents($resIdFile));
      if (intval($resId) !== $tmpResId) {
        if ($this->isRunning($pid)) {
          return true;//$this->stopServer();
        }
        return false;
      } else {
        return $this->isRunning($pid);
      }
    } else {
      return false;
    }
  }

  public function launchNodeServer($resId) {
    $cmd = "npm --prefix $this->COMPONENTS_ROUTE run serve:$resId";
    $outputfile = $this->COMPONENTS_ROUTE . 'npmOutPut';
    $pidfile = $this->COMPONENTS_ROUTE . 'pidFile';
    $resIdFile = $this->COMPONENTS_ROUTE . 'resId';
    //file_put_contents($outputfile, "");
    //file_put_contents($pidfile, "");
    $isRunning = false;
    if (file_exists($pidfile)) {
      //first check if the id still the same
      $pid = file_get_contents($pidfile);
      $tmpResId = intval(file_get_contents($resIdFile));
      if (intval($resId) !== $tmpResId) {
        shell_exec(sprintf("kill %d", $pid));
        shell_exec("pkill node");
        $this->cleanAndStartDevServer($cmd, $resIdFile, $pidfile, $resId, $outputfile);
      } elseif (!$this->isRunning($pid)) {
        $this->cleanAndStartDevServer($cmd, $resIdFile, $pidfile, $resId, $outputfile);
      }

    } else {
      $this->cleanAndStartDevServer($cmd, $resIdFile, $pidfile, $resId, $outputfile);
    }
  }

  public function stopServer() {
    $pidfile = $this->COMPONENTS_ROUTE . 'pidFile';
    if (file_exists($pidfile)) {
      shell_exec("pkill node");
    }

  }

  private function cleanAndStartDevServer($cmd, $resIdFile, $pidfile, $resId, $outputfile) {
    file_put_contents($resIdFile, '');
    file_put_contents($pidfile, '');
    file_put_contents($outputfile, '');
    file_put_contents($resIdFile, $resId);
    exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
  }

  private function isRunning($pid){
    try{
      $result = shell_exec(sprintf("ps %d", $pid));
      if( count(preg_split("/\n/", $result)) > 2){
        return true;
      }
    }catch(Exception $e){}

    return false;
  }


  /**
   *  Function that loadas the blocks for a given resource from the database
   * @param $resId resourceId
   **/
  private function loadResourceBlocks($resId) {
    $query = $this->modx->query(" 
      select blocks.chunkName, resourceContent.*
      from modx_vlox_blocks as blocks, modx_vlox_resource_content as resourceContent
      where blocks.id = resourceContent.blockId
      and resourceContent.resourceId = $resId 
      order by resourceContent.position");

    if (is_null($query)) {
      throw new Exception("Block content not found for 
                        getResourcesContent(resourceId: $resId)");
    }
    return $query;
  }

  /**
   * Taken from https://stackoverflow.com/questions/2050859/copy-entire-contents-of-a-directory-to-another-using-php
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

    if (is_dir($destinationDirectory) === false) {
      mkdir($destinationDirectory);
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