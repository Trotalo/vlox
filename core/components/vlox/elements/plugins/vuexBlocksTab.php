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
        //$assetsLocation = $modx->getOption('vlox.assets_url');
        $assetsLocation =  $modx->getOption('vlox.assets_url', null,
                        $modx->getOption('assets_url') . 'components/vlox/');
        $url = $assetsLocation. 'vloxTab.html' ;
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