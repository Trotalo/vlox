<?php
switch ($modx->event->name) {
// Add a custom tab to the MODX create/edit resource pages
  case 'OnDocFormPrerender':
    //validate that its an existing resource, and it has the right template
    if ($mode === "upd") {
      $template = $modx->getObject('modTemplate', array('templatename' => 'krakenTemplate'));
      if ($template->id === $resource->template) {
        $url = $modx->getOption('kraken.monster_dev') ?
                        '/kraken/assets/components/kraken/krakenTab.html' :
                        '/assets/components/kraken/krakenTab.html';
        $modx->regClientStartupHTMLBlock(' 
          <script type="text/javascript">
              MODx.on("ready",function() {
                  MODx.addTab("modx-resource-tabs",{
                      title: "KrakenBlocks",
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