<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

switch ($modx->event->name) {
// Add a custom tab to the MODX create/edit resource pages
  case 'OnDocFormPrerender':
    //validate that its an existing resource, and it has the right template
    if ($mode === "upd") {
      $template = $modx->getObject('modTemplate', array('templatename' => 'vloxTemplate'));
      if ($template->id === $resource->template) {
        //Before moving on, we make sure friendly url si enabled
        $setting = $modx->getObject('modSystemSetting', 'friendly_urls');
        //check the locked status
        if($setting->get('value') !== 1) {
          $setting->set('value', 1);
          $setting->save();
          $cacheRefreshOptions =  array( 'system_settings' => array() );
          $modx->cacheManager-> refresh($cacheRefreshOptions);
        }

        $assetsLocation =  $modx->getOption('vlox.assets_url', null,
                        $modx->getOption('assets_url') . 'components/vlox/');
        $url = $assetsLocation. 'vloxTab.html' ;
        //$url = "https://" .$_SERVER['SERVER_NAME'] . "/resEditor.html?resId=" . $resource->id;
        $modx->regClientStartupHTMLBlock(' 
          <script type="text/javascript">
              MODx.on("ready",function() {
                  MODx.addTab("modx-resource-tabs",{
                      title: "VloX",
                      id: "custom-resource-tab",
                      width: "95%",
                      autoLoad: {
                        url: "' . $url . '",
                        scripts : true
                      }
                  });
              });                
          </script>'
          );
      }
    }
    break;

}