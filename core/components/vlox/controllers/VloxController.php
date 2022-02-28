<?php
require_once MODX_BASE_PATH . 'config.core.php';
require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';


use ScssPhp\ScssPhp\Compiler;



/**
 * Loads the json from vlox_resource_content table
 */
class VloxController {

  /** @var modX $modx */
  private $modx;
  /** @var ScssPhp\ScssPhp\Compiler $scss*/
  private $scss;

  private $COMPONENTS_ROUTE; //'vlox/assets/components/vlox/renderedBlocks/'

  function __construct() {
    $this->modx = new modX;
    $this->scss = new Compiler();
    $this->modx->initialize('web');
    //$packagePath = $this->modx->getOption('vlox.core_path') . 'model/';
    $packagePath = $this->modx->getOption('vlox.core_path', null,
        $this->modx->getOption('core_path') . 'components/vlox/'). 'model/';
    if (!$this->modx->addPackage('vlox', $packagePath)) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, "vlox package not found");
      throw new Exception("vlox package not found");
    }
    //$assetsLocation = $this->modx->getOption('vlox.assets_path');
    $assetsLocation = $this->modx->getOption('vlox.assets_path', null,
                                      $this->modx->getOption('assets_path') . 'components/vlox/');
    $this->COMPONENTS_ROUTE = $assetsLocation . 'renderedBlocks/';
  }

  /** @param modX $modx */
  public static function loadService($modx): void {
    $admUserMgr = $modx->getService('VloxController',
      'VloxController',
      MODX_CORE_PATH . 'components/vlox/controllers');
    if (!($admUserMgr instanceof VloxController)) {
      $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load VloxResourceContentTable class');
    }
  }

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

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      //$compName = str_replace('.vue', $row['id'], $chunkName);
      $compName = strtolower($chunkName . '-' . $resBlockId);
      $blockContent = $this->buildJsonContent($chunkName, $row['properties'], $compName);
      if (empty($blockContent)) {
        throw new Exception($chunkName . ' snippet not found, check your manager configuration!');
      }
      //Build the final final usign DOM parser lib
      /*$document = new \Gt\Dom\HTMLDocument($blockContent);
      $styleSection = $document->querySelector("style")->textContent;*/
      $styleStart = strpos($blockContent, '<style');
      $styleEnd = strpos($blockContent, '</style>');
      $styleSection = substr($blockContent, $styleStart + 13, ($styleEnd - $styleStart) - 13) ;
      try {
        $compiledStyle = $this->scss->compile($styleSection);
      } catch (\Exception $e) {
        throw new Exception("There are issues with $chunkName, error is $e->getMessage()" );
      }
      $blockContent = substr($blockContent, 0, strpos($blockContent, '<style'));
      $blockContent .= "<style scope>";
      $blockContent .= $compiledStyle;
      $blockContent .= "</style>";


      //--------------TEST CODE
      // Get the modParser instance
      $parser = $this->modx->getParser();

      // Define how deep we can go
      $maxIterations= (integer) $this->modx->getOption('parser_max_iterations', null, 10);

      // Parse cached tags, while leaving unprocessed tags in place
      $parser->processElementTags('', $blockContent, false, false, '[[', ']]', [], $maxIterations);
      // Parse uncached tags and remove anything that could not be processed
      $parser->processElementTags('', $blockContent, true, true, '[[', ']]', [], $maxIterations);

      //echo $blockContent;

      //$document->querySelector("style")->textContent = $compiledStyle;
      $finalBlock = $blockContent;
      //And finally we save the partial vue component
      //$vueFileName = MODX_BASE_PATH . 'modxMonster/renderedBlocks/' . $compName . '.vue';
      $vueFileName = $this->COMPONENTS_ROUTE . $compName . '.vue';
      $vueFile = fopen($vueFileName, "w");
      if (!$vueFile) {
        $lastError = error_get_last();
        throw new Exception("Problems creating $vueFileName error was: $lastError");
      }
      fwrite($vueFile, $finalBlock);
      fclose($vueFile);

    }
    /*if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }*/
    //return '';
  }

  public function renderComponentImports($resId) {
    $returnValue = "components: { ";
    $query = $this->loadResourceBlocks($resId);

    while ($row = $query->fetch()) {
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR, json_encode($row));
      $chunkName = $row['chunkName'];
      $resBlockId = $row['id'];
      //$compName = strtolower(str_replace('.vue', '-' . $resBlockId, $chunkName));\
      $compName = strtolower($chunkName . '-' . $resBlockId);
      //$fileName = str_replace('.vue', $resBlockId . '.vue', $chunkName);
      $fileName = $compName . '.vue';
      $returnValue .= "'$compName': httpVueLoader('". $this->modx->getOption('site_url') .
        str_replace(MODX_BASE_PATH, '', $this->COMPONENTS_ROUTE) . "$fileName'), ";
    }
    $returnValue .= "}";
    if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
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
      $compName = strtolower($chunkName . '-' . $resBlockId);
      $scrollDiv = '<div id="' . $row['id'] . '-' . $row['title'] . '">';
      $returnValue .= $scrollDiv;
      $returnValue .= "<$compName v-on:toggle-loading=\"toggleLoading\" v-on:show-error=\"toggleError\" ></$compName>";
      $returnValue .= "</div>";
    }
    if (empty($returnValue)) {
      $returnValue = "<h1>There aren't any blocks assigned to this resource</h1>";
    }
    return $returnValue;
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
}