<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

use MODX\Revolution\Rest\modRestController;
use MODX\Revolution\Rest\modRestServiceRequest;

class VloxBlocks extends  modRestController {

  /** @var string $classPrefix */
  public $vloxPrefix;

  /** @var string $classPrefix */
  public $modxPrefix;

  /** @var string $classKey The xPDO class to use */
  public $classKey = 'Vlox\Model\VloxFragments';

  /** @var string $classAlias The alias of the class when used in the getList method */
  public $classAlias  = 'VloxFragments';

  /** @var string $defaultSortField The default field to sort by in the getList method */
  public $defaultSortField = 'id';
  /** @var string $defaultSortDirection The default direction to sort in the getList method */
  public $defaultSortDirection = 'ASC';

  public $primaryKeyField = 'id';

  private $defaultContent = <<<STR
<template>
  <p>Create some content!</p>
</template>
<style scoped lang="scss">
//Place styles here
</style>
STR;

  public function __construct(modX $modx,modRestServiceRequest $request,array $config = array()){
    parent::__construct($modx, $request, $config);
    $isMODX3 = $modx->getVersionData()['version'] >= 3;
    $this->vloxPrefix = $isMODX3 ? 'Vlox\Model\\' : '';
    $this->modxPrefix = $isMODX3 ? 'MODX\Revolution\\' : '';
  }


  /**
   * This method can receive a number, to search for the component, or by name, to validate
   * if a give block exists,
   *
   * this method also associates the block with the renderer to be shown, this is basicaly due the fact
   * that if the user is posting changes is because he is on the editing resource
   * @param string $id
   */
  public function read($id) {

    if (is_numeric( $id )) {
      /** @var vloxBlock $respurce */
      parent::read($id);
      //with the stored block we read the file and retrieve ewach section to be editable on the front
      $chunkName = $this->object->get('chunkName');
      $chunk = $this->modx->getObject($this->modxPrefix . 'modChunk', array('name'=>$chunkName));
      $blockContent = $chunk->get('snippet');

      $returnObject = array(
        'htmlSection' => $blockContent,
        //'scriptSection' => $scriptSection,
        //'styleSection' => $styleSection
      );
      $returnObject =  array_merge( $returnObject, (array) $this->object->_fields);
      //Before creating a new registry, we make sure the respurce its clean
      //first we make sure that the vlox renderer exists
      $renderer = $this->modx->getObject($this->modxPrefix . 'modResource', array('pagetitle' => 'vloxrenderer'));
      if (empty($renderer)) {
        throw new Error("FATAL: RENDERER NOT FOUND!!!!!");
        //If the resources isn't crated
        //Load the vlox template
        /*
         * This section was itneded to create the base resources needed to render the components
         * but since the issue with GPM was fixed and now we can package resopurces, this is no
         * longer needed, but the code piece could be usefull
         *
        $template = $this->modx->getObject('modTemplate', array('templatename' => 'vloxTemplate'));

        $renderer = $this->modx->newObject('modResource',
          array('pagetitle'=> 'vloxrenderer',
            'longtitle'=> 'vloxrenderer',
            'description'=>'vloxrenderer',
            'alias'=> 'vloxrenderer',
            'template'=> $template->id,
            'published'=> 1));
        $renderer->save();
        //Finally, we set the friendly url on, so we can load the right elements
        $setting = $this->modx->getObject('modSystemSetting', 'friendly_urls');
        //check the locked status
        $setting->set('value', 1);
        $setting->save();
        $cacheRefreshOptions =  array( 'system_settings' => array() );
        $this->modx->cacheManager-> refresh($cacheRefreshOptions);*/

      }
      //We check if the friendUrl setting is enabled
      $setting = $this->modx->getObject($this->modxPrefix . 'modSystemSetting', 'friendly_urls');
      //check the locked status
      if($setting->get('value') !== 1) {
        $setting->set('value', 1);
        $setting->save();
        $cacheRefreshOptions =  array( 'system_settings' => array() );
        $this->modx->cacheManager-> refresh($cacheRefreshOptions);
      }

      $this->modx->removeCollection( $this->vloxPrefix . 'VloxResourceContent', array('resourceId'=> $renderer->id));
      /** @var VloxResourceContent $blockPreviewObj */
      $blockPreviewObj = $this->modx->newObject($this->vloxPrefix . 'VloxResourceContent',
        array('position'=> 1,
          'title'=> 'compoRenderer',
          'description'=>'compoRenderer',
          'blockId'=> $this->object->_fields['id'],
          'resourceId'=> $renderer->id,
          'properties'=> $this->object->_fields['properties']));
      $blockPreviewObj->save();
      $this->modx->cacheManager->refresh();
      return $this->success('Succesful call!', $returnObject);
    } else {
      $block = $this->modx->getObject('vloxBlocks', array('chunkName'=>$id));
      return $this->success('Succesful call!', $block);
    }

  }

  /**
   * @return array|void
   */
  public function put() {
    $vloxBlocks = null;
    $resContent = $this->getProperties();
    $chunkName = $resContent['chunkName'];
    $description = $resContent['description'];
    $properties = json_decode( $resContent['properties'], true);
    $vloxType = 0;
    if (!empty($properties)){
      $vloxType = $properties['type'];
    }
    $coreLocation = $this->modx->getOption('vlox.core_path', null,
      $this->modx->getOption('core_path') . 'components/vlox/');
    require_once($coreLocation . 'controllers/VloxController.php');

    VloxController::loadService($this->modx, 'VloxController');
    //TODO here we must change to the new loading stratey and have the load in a seprate function
    $chunk = $this->modx->getObject('modChunk', array('name'=>$chunkName));
    if (isset($chunk)) {
      //With te stored chunck now we
      $chunk->set('snippet', $resContent['htmlSection']);
      $chunk->save();
    } else {
      //First we get the cat id for vlox
      $category = $this->modx->getObject('modCategory', array('category'=> 'Vlox'));
      if (empty($category)) {
        throw new Exception("category Vlox not found, please reinstall the plugin");
      }
      $content = $this->defaultContent;
      if ($vloxType === 1) {
        $content = str_replace("[[+componentName]]", $chunkName, $content);
      }

      /** @var modChunk $chunk */
      $chunk = $this->modx->newObject('modChunk',
                              array('name'=>$chunkName,
                                    'category'=>$category->id,
                                    'snippet'=>$content,
                                    'content'=>$content));
      $chunk->save();
      //and now we store the data into the vlox table
      /** @var vloxBlocks $vloxBlocks */
      $vloxBlocks = $this->modx->newObject('Vlox\Model\VloxFragments');
      $vloxBlocks->set('chunkName',$chunkName);
      $vloxBlocks->set('title',$chunkName);
      $vloxBlocks->set('description',$description);
      $objectToStore = (object)['type'=> $vloxType];
      $vloxBlocks->set('properties',json_encode($objectToStore));
      $vloxBlocks->save();

      //return $this->read($vloxBlocks->get('id'));
    }//TODO aca epieza la trama para poner  quitar el parametro
    if ($vloxType === 1) {
      $this->modx->VloxController->generateGlobalComponents();
    }
    //Finally regenerate the global component files
    //generateVueComponentsFiles
    $renderer = $this->modx->getObject('modResource', array('pagetitle' => 'vloxrenderer'));
    if (is_null($renderer)) {
      throw new Exception('vloxrenderer not found!');
    }
    $this->modx->VloxController->generateVueComponentsFiles($renderer->get('id'), true);
    $vloxBlocks = is_null($vloxBlocks) ? $chunk : $vloxBlocks;
    return $this->success('updated', $vloxBlocks);
  }

  public function delete() {
    //First we retrieve the block to get the chunkName
    $id = $this->getProperty($this->primaryKeyField,false);
    if (empty($id)) {
      return $this->failure($this->modx->lexicon('rest.err_field_ns',array(
        'field' => $this->primaryKeyField,
      )));
    }
    $block = $this->modx->getObject('vloxBlocks', $id);
    //Now we check if the block is in use, its is the case an error should be thrown
    $resBlocks = $this->modx->getCollection('vloxResourceContent', array('blockId' => $id) );
    if (isset($resBlocks) && !empty($resBlocks)) {
      if (sizeof($resBlocks) === 1 && array_values($resBlocks)[0]->title === 'algo') {
        array_values($resBlocks)[0]->remove();
        //check if the only place used is the single renderer
      } else {
        return $this->failure("Can't delete block, its used on one or more resources");
      }
    }
    //Finally if its possible we delete both the block and the chunk

    $chunk = $this->modx->getObject('modChunk', array('name' => $block->chunkName));
    if ($chunk) $chunk->remove();

    parent::delete();
  }

  public function post() {

    $assetsLocation = $this->modx->getOption('vlox.assets_path', null, $this->modx->getOption('assets_path') . 'components/vlox/');

    $id = $this->properties['id'];
    if (!is_null($id)) {
      $resBlocks = $this->modx->getObject('vloxResourceContent', array('resourceId' => $id) );
      $vlox =  $this->modx->getObject('vloxBlocks', array('id' => $resBlocks->get('blockId')) );
      $fileName = $vlox->get('title') . '.png';
      if (!is_dir($assetsLocation . 'compoSnapshots')) {
        mkdir($assetsLocation . 'compoSnapshots');
      }
      if (!move_uploaded_file($_FILES["img"]["tmp_name"], $assetsLocation . 'compoSnapshots/' . $fileName)) {
        $this->modx->log(modX::LOG_LEVEL_ERROR, "Image failed to store!!!");

      }
    }
  }
}