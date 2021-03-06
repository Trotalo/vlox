<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

class KrakenResources extends modRestController {
  /** @var string $classKey The xPDO class to use */
  public $classKey = 'vloxResourceContent';
  /** @var string $defaultSortField The default field to sort by in the getList method */
  public $defaultSortField = 'position';
  /** @var string $defaultSortDirection The default direction to sort in the getList method */
  public $defaultSortDirection = 'ASC';

  /**
   * Saves a change on the contents of a block inside a resource
   */
  function post() {
    $properties = $this->getProperties();
    if (!empty($properties) && count($properties) > 0) {
      $resId = $properties['id'];
      if (is_null($resId)) {
        //new object
        //first we need to retrieve the current max position
        $query = $this->modx->query(" 
          select max(resourceContent.position) as position
          from modx_vlox_resource_content as resourceContent");
        if (is_null($query)) {
          throw new Exception("No data found for position, maybe new res??");
        }

        $row = $query->fetch();
        $currentMaxPost = isset($row['position']) ? intval($row['position']) : 0;
        $currentMaxPost = ++$currentMaxPost;
        $resBlock = $this->modx->newObject('vloxResourceContent', array(
          'position' => $currentMaxPost,
          'title' => $properties["title"],
          'description' => 'Add a description!',
          'blockId' => $properties["blockId"],
          'resourceId' => $properties["resourceId"],
          'counter' => 0,
          'properties' => $properties["properties"]
        ));
        $resBlock->save();
      } elseif (!is_null($properties['items'])) {
        $resId = $properties['id'];
        $blockId = $properties['blockId'];
        /** @var vloxResourceContent $resource */
        /*$resource = $this->modx->getObject('vloxResourceContent', array(
          'blockId' => $blockId,
          'resourceId' => $resId
        ));*/
        $resource = $this->modx->getObject('vloxResourceContent', ['id' => $resId]);
        if (is_null($resource)) {
          return $this->failure("Block content with blockId: $blockId resId: $resId not found!");
        }
        $resource->set('properties', $properties);
        $saveResponse = $resource->save();
        if ($saveResponse) {
          $this->modx->cacheManager->refresh();
          return $this->success('Succesful call!');
        } else {
          return $this->success('Issues storing yur info!');
        }
      } else {
        return $this->failure('Missing block items!');
      }
    } else {
      return $this->failure('Missing message body!');
    }
  }

  /**
   * @return array|void
   */
  public function put() {
    $resContent = $this->getProperties();
    if (isset($resContent)) {
      foreach ($resContent as $blockContent) {
        //** @var vloxResourceContent  $resBlockContent*/
        $resBlockContent = $this->modx->getObject('vloxResourceContent', $blockContent['id']);
        $resBlockContent->set('position', $blockContent['position']);
        $resBlockContent->save();
        $this->modx->log(ModX::LOG_LEVEL_DEBUG, json_encode($resBlockContent));
      }
      $this->modx->cacheManager->refresh();
    }
  }

  /**
   * Overwriting of standard get to retrieve the resources
   * @return array|void
   */
  public function get() {
    $pk = $this->getProperty($this->primaryKeyField);

    $this->modx->log(modX::LOG_LEVEL_INFO, "LoadingRespoure $pk");
    if (empty($pk)) {
      return $this->getList();
    }
    $objects = $this->modx->getCollection(
            'vloxResourceContent',
                      ['resourceId'=> $pk]);
    //$string = print_r($objects, true);
    //$this->modx->log(modX::LOG_LEVEL_ERROR, "database info $string");
    if (empty($objects)) $objects = array();
    $list = array();
    /** @var xPDOObject $object */
    foreach ($objects as $object) {
      $list[] = $this->prepareListObject($object);
    }
    return $this->collection($list);
  }

  /**
   * Used to clean the cache after a change into the database
   * @param array $objectArray
   */
  public function afterDelete(array &$objectArray) {
    $this->modx->cacheManager->refresh();
  }

}
