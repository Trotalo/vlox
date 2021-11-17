<?php


class KrakenInitManagerController  extends modExtraManagerController {

  public function initialize() {
    return parent::initialize();
  }

  public function process(array $scriptProperties = array()) {}

  public function getPageTitle() {
    return 'ModxMonster Blocks editor';
  }
  public function getTemplateFile() {
    //return '/var/www/html/kraken/assets/components/kraken/blocksEditor/blocksEditor.html';
    //$assetsLocation = $this->modx->getOption('kraken.assets_path');
    $assetsLocation = $this->modx->getOption('kraken.assets_path', null, $this->modx->getOption('assets_path') . 'components/kraken/');
    return $assetsLocation . 'blocksEditor/blocksEditor.html';
  }
}