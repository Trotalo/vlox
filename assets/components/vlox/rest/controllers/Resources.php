<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

$coreLocation = $this->modx->getOption('vlox.core_path', null,
  $this->modx->getOption('core_path') . 'components/vlox/');
require_once($coreLocation . 'controllers/VloxController.php');

class VloxResources extends \MODX\Revolution\Rest\modRestController {
  /** @var string $classKey The xPDO class to use */
  public $classKey = 'Vlox\Model\VloxResourceContent';
  /** @var string $classAlias The alias of the class when used in the getList method */
  public $classAlias  = 'VloxResourceContent';
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
        $resBlock = $this->modx->newObject($this->classKey, array(
          'position' => $currentMaxPost,
          'title' => $properties["title"],
          'description' => 'Add a description!',
          'blockId' => $properties["blockId"],
          'resourceId' => $properties["resourceId"],
          'counter' => 0,
          'properties' => $properties["properties"]
        ));
        $resBlock->save();
        VloxController::loadService($this->modx, 'VloxController');
        $this->modx->VloxController->generateVueComponentsFiles($properties["resourceId"], false);
      } elseif (!is_null($properties['items'])) {
        $resId = $properties['id'];
        $blockId = $properties['blockId'];
        /** @var vloxResourceContent $resource */
        /*$resource = $this->modx->getObject('vloxResourceContent', array(
          'blockId' => $blockId,
          'resourceId' => $resId
        ));*/
        $resource = $this->modx->getObject($this->classKey, ['id' => $resId]);
        if (is_null($resource)) {
          return $this->failure("Block content with blockId: $blockId resId: $resId not found!");
        }
        $resource->set('properties', $properties);
        $saveResponse = $resource->save();
        if ($saveResponse) {
          $this->modx->cacheManager->refresh();
          VloxController::loadService($this->modx, 'VloxController');
          $this->modx->VloxController->generateVueComponentsFiles($resId, false);
          $this->success('Succesful call!');
        } else {
          $this->success('Issues storing yur info!');
        }
      } else {
        $this->failure('Missing block items!');
      }
    } else {
      $this->failure('Missing message body!');
    }
  }

  /**
   * @return array|void
   */
  public function put() {
    $resContent = $this->getProperties();
    $resId = 0;
    if (isset($resContent)) {
      foreach ($resContent as $blockContent) {
        //** @var vloxResourceContent  $resBlockContent*/
        $resBlockContent = $this->modx->getObject($this->classKey, $blockContent['id']);
        $resBlockContent->set('position', $blockContent['position']);
        $resId = $blockContent['resourceId'];
        $resBlockContent->save();
        $this->modx->log(ModX::LOG_LEVEL_DEBUG, json_encode($resBlockContent));
      }
      $this->modx->cacheManager->refresh();
      VloxController::loadService($this->modx, 'VloxController');
      $this->modx->VloxController->generateVueComponentsFiles($resId, false);
    }
  }

  /**
   * Overwriting of standard get to retrieve the resources
   * @return array|void
   */
  public function get() {
    $pk = $this->getProperty($this->primaryKeyField);
    if (empty($pk)) {
      return $this->getList();
    }
    $vloxId = $this->getProperty('vloxId');
    $query = null;
    if (!empty($vloxId)) {
      $query = ['resourceId'=> $pk, 'blockId' => $vloxId];
    } else {
      $query = ['resourceId'=> $pk];
    }

    $objects = $this->modx->getCollection( $this->classKey, $query);
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
    VloxController::loadService($this->modx, 'VloxController');
    $this->modx->VloxController->generateVueComponentsFiles($objectArray['resourceId'], false);
    $this->modx->cacheManager->refresh();
  }
}
