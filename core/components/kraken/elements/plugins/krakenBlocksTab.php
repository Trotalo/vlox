<?php
switch ($modx->event->name) {
// Add a custom tab to the MODX create/edit resource pages
  case 'OnDocFormPrerender':
    $modx->regClientStartupHTMLBlock('
    <script type="text/javascript">
        MODx.on("ready",function() {
            MODx.addTab("modx-resource-tabs",{
                title: "KrakenBlocks",
                id: "custom-resource-tab",
                width: "95%",
                autoLoad: {
                	url: "/kraken/assets/components/kraken/krakenTab.html",
                	scripts : true
                }
            });
        });                
    </script>'
    );
}