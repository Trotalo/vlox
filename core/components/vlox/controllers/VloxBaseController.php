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

  /** @var string $classPrefix */
  public $vloxPrefix;

  /** @var string $classPrefix */
  public $modxPrefix;

  /** @var modX $modx */
  protected $modx;

  /** @var string $assetsLocation */
  protected $assetsLocation;

  protected $basePath;
  protected $COMPONENTS_ROUTE;

  function __construct() {
    $this->modx = new modX;
    $this->modx->initialize('web');
    //$packagePath = $this->modx->getOption('vlox.core_path') . 'model/';
    $packagePath = $this->modx->getOption('vlox.core_path', null,
        $this->modx->getOption('core_path') . 'components/vlox/'). 'src/Model/';
    if (!$this->modx->addPackage('Vlox', $packagePath)) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, "vlox package not found");
      throw new Exception("vlox package not found");
    }
    //$assetsLocation = $this->modx->getOption('vlox.assets_path');
    $this->assetsLocation = $this->modx->getOption('vlox.assets_path', null,
      $this->modx->getOption('assets_path') . 'components/vlox/');
    $coreLocation = $this->modx->getOption('vlox.core_path', null,
      $this->modx->getOption('core_path') . 'components/vlox/');
    $this->COMPONENTS_ROUTE = $coreLocation . 'vue3-res/';
    //TODO delete this section after development
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $this->basePath = $this->modx->config['base_path'];

    $isMODX3 = $this->modx->getVersionData()['version'] >= 3;
    $this->vloxPrefix = $isMODX3 ? 'Vlox\Model\\' : '';
    $this->modxPrefix = $isMODX3 ? 'MODX\Revolution\\' : '';
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

  protected function deleteFilesFromFolder($route) {
    $files = glob($route); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file)) {
        unlink($file); // delete file
      }
    }
  }

}