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
    return $this->modx->getOption('kraken.monster_dev') ?
                      '/var/www/html/kraken/assets/components/kraken/blocksEditor/blocksEditor.html' :
                      '/var/www/html/assets/components/kraken/blocksEditor/blocksEditor.html';
  }
}