<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

class VloxBaseController {

  /** @var modX $modx */
  protected $modx;

  /** @var string $assetsLocation */
  protected $assetsLocation;

  protected $COMPONENTS_ROUTE; //'vlox/assets/components/vlox/renderedBlocks/'

  function __construct() {
    $this->modx = new modX;
    $this->modx->initialize('web');
    //$packagePath = $this->modx->getOption('vlox.core_path') . 'model/';
    $packagePath = $this->modx->getOption('vlox.core_path', null,
        $this->modx->getOption('core_path') . 'components/vlox/'). 'model/';
    if (!$this->modx->addPackage('vlox', $packagePath)) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, "vlox package not found");
      throw new Exception("vlox package not found");
    }
    //$assetsLocation = $this->modx->getOption('vlox.assets_path');
    $this->assetsLocation = $this->modx->getOption('vlox.assets_path', null,
      $this->modx->getOption('assets_path') . 'components/vlox/');
    $coreLocation = $this->modx->getOption('vlox.core_path', null,
      $this->modx->getOption('core_path') . 'components/vlox/');
    $this->COMPONENTS_ROUTE = $coreLocation . 'vue-res/';
  }

  /** @param modX $modx */
  public static function loadService($modx, $controllerName): void {
    $admUserMgr = $modx->getService($controllerName,
      $controllerName,
      MODX_CORE_PATH . 'components/vlox/controllers');
    if (!(is_a($admUserMgr, $controllerName) )) {
      $modx->log(modX::LOG_LEVEL_ERROR,
        "Could not load $admUserMgr class");
    }
  }

}