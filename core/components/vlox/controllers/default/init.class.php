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
    return 'ModxMonster Blocks editor';
  }
  public function getTemplateFile() {
    //return '/var/www/html/vlox/assets/components/vlox/blocksEditor/blocksEditor.html';
    //$assetsLocation = $this->modx->getOption('vlox.assets_path');
    $assetsLocation = $this->modx->getOption('vlox.assets_path', null, $this->modx->getOption('assets_path') . 'components/vlox/');
    return $assetsLocation . 'blocksEditor/vloxEditor.html';
  }
}