<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

class VloxManageManagerController extends VloxBaseManagerController
{

  public function process(array $scriptProperties = []): void
  {
  }

  public function getPageTitle(): string
  {
    return $this->modx->lexicon('vlox');
  }

  /*public function loadCustomCssJs(): void
  {
      $this->addLastJavascript($this->vlox->getOption('jsUrl') . 'mgr/widgets/manage.panel.js');
      $this->addLastJavascript($this->vlox->getOption('jsUrl') . 'mgr/sections/manage.js');

      $this->addHtml(
          '
          <script type="text/javascript">
              Ext.onReady(function() {
                  MODx.load({ xtype: "vlox-page-manage"});
              });
          </script>
      '
      );
  }*/

  public function getTemplateFile(): string
  {
    //return $this->vlox->getOption('templatesPath') . 'manage.tpl';
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
