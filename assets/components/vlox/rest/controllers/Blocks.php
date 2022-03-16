<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

class KrakenBlocks extends  modRestController {
  /** @var string $classKey The xPDO class to use */
  public $classKey = 'vloxBlocks';
  /** @var string $defaultSortField The default field to sort by in the getList method */
  public $defaultSortField = 'id';
  /** @var string $defaultSortDirection The default direction to sort in the getList method */
  public $defaultSortDirection = 'ASC';

  private $defaultContent = <<<STR
      <template>
      <p>Create some content!</p>
      </template>
      <script>
      export default {
        name: "[[+componentName]]",
        data() {
          return {
            vloxBlock: [[+blockContent]]
          };
        },
      };
      </script>
      <style scope lang="scss">
      //Place styles here
      </style>
    STR;

  public function __construct(modX $modx,modRestServiceRequest $request,array $config = array()){
    parent::__construct($modx, $request, $config);
  }


  /**
   * This method can receive a number, to search for the component, or by name, to validate
   * if a give block exists
   * @param string $id
   */
  public function read($id) {

    if (is_numeric( $id )) {
      /** @var vloxBlock $respurce */
      parent::read($id);
      //with the stored block we read the file and retrieve ewach section to be editable on the front
      $chunkName = $this->object->get('chunkName');
      $chunk = $this->modx->getObject('modChunk', array('name'=>$chunkName));
      $blockContent = $chunk->get('snippet');

      $returnObject = array(
        'htmlSection' => $blockContent,
        //'scriptSection' => $scriptSection,
        //'styleSection' => $styleSection
      );
      $returnObject =  array_merge( $returnObject, (array) $this->object->_fields);
      //Before creating a new registry, we make sure the respurce its clean
      //first we make sure that the vlox renderer exists
      $renderer = $this->modx->getObject('modResource', array('pagetitle' => 'vloxrenderer'));
      if (empty($renderer)) {
        //If the resources isn't crated
        //Load the vlox template
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
        $setting->set('value', 1);
        $setting->save();
        $cacheRefreshOptions =  array( 'system_settings' => array() );
        $this->modx->cacheManager-> refresh($cacheRefreshOptions);

      }

      $this->modx->removeCollection('vloxResourceContent', array('resourceId'=> $renderer->id));
      /** @var vloxResourceContent $blockPreviewObj */
      $blockPreviewObj = $this->modx->newObject('vloxResourceContent',
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
    $resContent = $this->getProperties();
    $chunkName = $resContent['chunkName'];
    $description = $resContent['description'];

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
      /** @var modChunk $chunk */
      $chunk = $this->modx->newObject('modChunk',
                              array('name'=>$chunkName,
                                    'category'=>$category->id,
                                    'snippet'=>$this->defaultContent,
                                    'content'=>$this->defaultContent));
      $chunk->save();
      //and now we store the data into the vlox table
      /** @var vloxBlocks $vloxBlocks */
      $vloxBlocks = $this->modx->newObject('vloxBlocks');
      $vloxBlocks->set('chunkName',$chunkName);
      $vloxBlocks->set('title',$chunkName);
      $vloxBlocks->set('description',$description);
      $objectToStore = (object)['name'=> $chunkName,
                                'items'=> []];
      $vloxBlocks->set('properties',json_encode($objectToStore));
      $vloxBlocks->save();

      //return $this->read($vloxBlocks->get('id'));
    }
    //Finally regenerate the component files
    //generateVueComponentsFiles
    $coreLocation = $this->modx->getOption('vlox.core_path', null,
      $this->modx->getOption('core_path') . 'components/vlox/');
    require_once($coreLocation . 'controllers/VloxController.php');

    VloxController::loadService($this->modx);
    $this->modx->VloxController->generateVueComponentsFiles(31);

    return $this->success('updated', $resContent);
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
}