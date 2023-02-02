<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

class VloxInitManagerController  extends modExtraManagerController {

  public function initialize() {
    return parent::initialize();
  }

  public function process(array $scriptProperties = array()) {}

  public function getPageTitle() {
    return 'Vlox editor';
  }
  public function getTemplateFile() {
    //Before moving on, we make sure friendly url si enabled
    $setting = $this->modx->getObject('modSystemSetting', 'friendly_urls');
    //check the locked status
    if($setting->get('value') !== 1) {
      $setting->set('value', 1);
      $setting->save();
      $cacheRefreshOptions =  array( 'system_settings' => array() );
      $this->modx->cacheManager-> refresh($cacheRefreshOptions);
    }

    $assetsLocation = $this->modx->getOption('vlox.assets_path', null, $this->modx->getOption('assets_path') . 'components/vlox/');
    return $assetsLocation . 'vloxContainer.html';
  }
}