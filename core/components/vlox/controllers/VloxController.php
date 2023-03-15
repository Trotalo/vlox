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



/**
 * Loads the json from vlox_resource_content table
 */
class VloxController extends  VloxBaseController {



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
      from modx_vlox_fragments as blocks, modx_vlox_resource_content as resourceContent
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
    $properties['componentName'] = $componentName;
    return $this->modx->getChunk($chunkName, $properties);
  }

  /**
   * Regenerates everithing to make sure that modx rendered objects are udpated
   */
  public function generateGlobalComponents() {
    $query = $this->modx->query(' 
                              SELECT *
                        FROM modx.modx_vlox_fragments
                        WHERE JSON_EXTRACT(properties, "$.type") = 1');

    if (is_null($query)) {
      //throw new Exception("NO global componments");
      //TODO this was changed du the fact that there cannot be global and no need of error
      return;
    }

    if (!file_exists($this->COMPONENTS_ROUTE . 'shared')) {
      mkdir($this->COMPONENTS_ROUTE . 'shared');
      mkdir($this->COMPONENTS_ROUTE . 'shared/components');
    }

    $this->deleteFilesFromFolder($this->COMPONENTS_ROUTE . 'shared/components/*');

    $parser = $this->modx->getParser();
    $maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);

    while ($row = $query->fetch()) {
      //remove folder contents the first time we fetch files
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      $compName = $chunkName;
      //TODO we need to get rid of this, this was the original idea for data admin, but we are using TVs and MIGx
      $blockContent = $this->buildJsonContent($chunkName, [], $compName);
      if (empty($blockContent)) {
        throw new Exception($chunkName . ' snippet not found, check your manager configuration!');
      }
      // Parse cached tags, while leaving unprocessed tags in place
      $parser->processElementTags('', $blockContent, false, false, '[[', ']]', [], $maxIterations);
      // Parse uncached tags and remove anything that could not be processed
      $parser->processElementTags('', $blockContent, true, true, '[[', ']]', [], $maxIterations);
      $finalBlock = $blockContent;
      //And finally we save the partial vue component
      //first check if the project dir exists
      $vueFileName = $this->COMPONENTS_ROUTE . 'shared/components/' . $compName . '.vue';
      $vueFile = fopen($vueFileName, "w");
      if (!$vueFile) {
        $lastError = error_get_last();
        throw new Exception("Problems creating $vueFileName error was: $lastError");
      }
      fwrite($vueFile, $finalBlock);
      fclose($vueFile);

    }

  }

    //TODO TEngo que hcaer una "copia" de la funcion de abajo
    // para generar los archivos shared, es decir componentes globales
  /**
   * @param $resId int
   * @return string
   */
  public function generateVueComponentsFiles($resId, $isEditingVlox) {
    if(is_null($resId) || $resId === 'undefined') {
      throw new Error("resId can't be null!");
    }
    $query = $this->loadResourceBlocks($resId);

    if (!file_exists($this->COMPONENTS_ROUTE . $resId)) {
      $this->recurseCopy($this->COMPONENTS_ROUTE . 'sample',
        $this->COMPONENTS_ROUTE . $resId);
    }
    $hasComponents = false;
    $parser = $this->modx->getParser();
    $maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);

    $currentResource = $this->modx->getObject($this->modxPrefix . 'modDocument', $resId);
    $vloxTemplate = $this->modx->getObject($this->modxPrefix . 'modTemplate', ['templatename' =>'vloxTemplate']);
    //Prepare resource tv's
    $pdoTvs = $currentResource->getTemplateVars();
    $resTvs = array();
    foreach ($pdoTvs as $tv) {
      $tvKey = $tv->get('name');
      $value = $tv->get('value');
      $resTvs[$tvKey] = $value;
    }
    while ($row = $query->fetch()) {
      //remove folder contents the first time we fetch files
      if (!$hasComponents) {
        $this->deleteFilesFromFolder($this->COMPONENTS_ROUTE . $resId . '/src/components/*');
      }
      $hasComponents = true;
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      $compName = $chunkName . '_' . $resBlockId; //strtolower($chunkName . '_' . $resBlockId);
      //TODO we need to get rid of this, this was the original idea for data admin, but we are using TVs and MIGx
      $blockContent = $this->buildJsonContent($chunkName, $resTvs, $compName);

      $vloxRenderer = $this->modx->getObject($this->modxPrefix . 'modResource', array('pagetitle' => 'vloxrenderer'));
      if (empty($vloxTemplate) || empty($vloxRenderer)) {
        throw new Error("Basic Vlox elements missing, please reinstall!");
      }
        //$this->modx->newObject('modResource');
      $vloxRenderer->set('template', $vloxTemplate->get('id'));
      $vloxRenderer->set('content', $blockContent);
      //set TVs
      foreach ( $resTvs as $tvKey => $tvVal){
        $vloxRenderer->setTVValue($tvKey, $tvVal);
      }
      $retVal =  $vloxRenderer->setContent($blockContent);

      $blockContent =  $vloxRenderer->parseContent();
      if (empty($blockContent)) {
        throw new Exception($chunkName . ' snippet not found, check your manager configuration!');
      }
      // Parse cached tags, while leaving unprocessed tags in place
      $parser->processElementTags('', $blockContent, false, false, '[[', ']]', [], $maxIterations);
      // Parse uncached tags and remove anything that could not be processed
      $parser->processElementTags('', $blockContent, true, true, '[[', ']]', [], $maxIterations);
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
    //if ($hasComponents) {
      //here we should recreate the App.vue file
    if (!$hasComponents) {
      $this->modx->log(xPDO::LOG_LEVEL_WARN, 'no components found, deleting files');
      $this->deleteFilesFromFolder($this->COMPONENTS_ROUTE . $resId . '/src/components/*');
    }
    //TODO here we need to set a TV that afects the defualtApp rendering
    if (is_null($isEditingVlox)) {
      $vloxType = false;
    }

    $output = $this->modx->getChunk('defaultApp', array('resId'=> $resId, 'isEditingVlox' => $isEditingVlox));

    $parser->processElementTags('', $output, false, false, '[[', ']]', [], $maxIterations);
    // Parse uncached tags and remove anything that could not be processed
    $parser->processElementTags('', $output, true, true, '[[', ']]', [], $maxIterations);
    $vueFileName = $this->COMPONENTS_ROUTE . $resId . '/src/App.vue';
    $vueFile = fopen($vueFileName, "w+");
    if (!$vueFile) {
      $lastError = error_get_last();
      throw new Exception("Problems creating $vueFileName error was: $lastError");
    }
    fwrite($vueFile, $output);
    fclose($vueFile);
    //Finally we check if the mainJs has been changed, and i it has,
    //we need to regenerate the file and restart the server
    $mainJs = $this->modx->getObject('modChunk', array('name'=>'mainJs'));
    $mainJsContent = $mainJs->get('snippet');
    $mainJsFileName = $this->COMPONENTS_ROUTE . $resId . '/src/main.js';
    $mainJsFile = fopen($mainJsFileName, "r+");
    if (!$mainJsFile) {
      $lastError = error_get_last();
      throw new Exception("Problems creating $mainJsFileName error was: $lastError");
    }
    $contents = fread($mainJsFile, filesize($mainJsFileName));
    if (strcmp($contents, $mainJsContent) !== 0 ) {
      ftruncate($mainJsFile, 0);
      //rewind($mainJsFile);
      fseek($mainJsFile, 0);
      $this->stopServer();
      fwrite($mainJsFile, $mainJsContent);
      $this->launchNodeServer($resId);
    }
    fclose($mainJsFile);
      //finally rewrite the mainJs flag to avoid reloading
    //}
  }

  public function renderComponentDef($resId) {
    $returnValue = "components: { ";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      //$compName = strtolower(str_replace('.vue', '-' . $resBlockId, $chunkName));\
      $compName = $chunkName . '_' . $resBlockId; //strtolower($chunkName . '_' . $resBlockId);
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
      $compName = $chunkName . '_' . $resBlockId; //strtolower($chunkName . '_' . $resBlockId);
      //$fileName = str_replace('.vue', $resBlockId . '.vue', $chunkName);
      $fileName = $compName;
      $returnValue .= "import $compName from './components/$compName.vue';\n";
    }
    return $returnValue;
  }

  //TODO ajustar este estilo, este se usa para ajustar el tamano en las ventana al imprimeir la iamgen del objeto pero luego
  // ya se tira la pagina toca es aplicar esto dinamicamente en la vista de lista
  public function renderComponentsTag($resId, $isEditingVlox) {
    $returnValue = "";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      $compName = $chunkName . '_' . $resBlockId; //strtolower($chunkName . '_' . $resBlockId);
      /*if (! is_null($isEditingVlox) && $isEditingVlox === '1' ) {
        $scrollDiv = '<div id="' . $row['id'] . '_' . $row['title'] . '" style="max-width: fit-content;">';
      } else {
        $scrollDiv = '<div id="' . $row['id'] . '_' . $row['title'] . '">';
      }*/
      $scrollDiv = '<div id="' . $row['id'] . '_' . $row['title'] . '">';
      //$scrollDiv = '<div id="' . $row['id'] . '_' . $row['title'] . '">';
      //$returnValue .= $scrollDiv;
      //$returnValue .= "<$compName v-on:toggle-loading=\"toggleLoading\" v-on:show-error=\"toggleError\" ></$compName>";
      $returnValue .= "<$compName />";
      //$returnValue .= "</div>";
    }
    if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }
    return $returnValue;
  }

  /**
   * Method in charge of updating the package.json file to run a given component
   * THIS IS FOR VUE2
   * @param $resId
   * @throws Exception
   */
  public function updatePackage($resId) {
    $packageFileLocation = $this->COMPONENTS_ROUTE . 'package.json';
    $jsonString = file_get_contents($packageFileLocation);
    $data = json_decode($jsonString, true);
    $data["scripts"] = array();
    $data["scripts"]["serve:$resId"] = "env APP_TYPE=$resId vue-cli-service serve $resId/src/main.js";
    $data["scripts"]["build:$resId"] = "env APP_TYPE=$resId vue-cli-service build $resId/src/main.js";
    /*array_push($data["scripts"], ("serve:$resId" => "env APP_TYPE=$resId vue-cli-service serve $resId/src/main.js"));
    array_push($data["scripts"], array( "build:$resId" => "env APP_TYPE=$resId vue-cli-service build $resId/src/main.js"));*/
    $newJsonString = json_encode($data, JSON_UNESCAPED_SLASHES);
    //check if the file can be writen is_writable
    if (is_writable($packageFileLocation)) {
      file_put_contents($packageFileLocation, $newJsonString);
    } else {
      throw new Exception('Wrong permissions for: ' + $packageFileLocation);
    }
  }

  /**
   * updatePackage for vite on VUE3
   * @param $resId
   * @throws Exception
   */
  public function updateViteConfig($resId, $build = false) {
    $packageFileLocation = $this->COMPONENTS_ROUTE . 'vite.config.js';


    $fileContents = $this->modx->getChunk('vite.config',
      array('project' => $resId, 'build' => $build));

    $parser = $this->modx->getParser();
    $maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);
    $parser->processElementTags('', $fileContents, false, false, '[[', ']]', [], $maxIterations);
    // Parse uncached tags and remove anything that could not be processed
    $parser->processElementTags('', $fileContents, true, true, '[[', ']]', [], $maxIterations);
    //$finalBlock = $blockContent;

    //check if the file can be writen is_writable
    if (is_writable($packageFileLocation)) {
      file_put_contents($packageFileLocation, $fileContents);
    } else {
      throw new Exception('Wrong permissions for: ' . $packageFileLocation);
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
  //TODO aca es el cambio para arranca el servidor de vue
  public function launchViteServer($resId) {

  }

  public function launchNodeServer($resId) {
    //$cmd = "npm --prefix $this->COMPONENTS_ROUTE run serve:$resId";
    $cmd = "npm --prefix $this->COMPONENTS_ROUTE run dev";
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
      from modx_vlox_fragments as blocks, modx_vlox_resource_content as resourceContent
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

  function parseInt($value) {
    if (is_null($value) || empty($value) ) {
      throw new Error('Cant parse empty values1');
    }
    return intval($value);
  }
}