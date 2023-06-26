<?php
/*require_once('/var/www/html/config.core.php');
require_once('/var/www/html/core/model/modx/modx.class.php');*/

use MODX\Revolution\modAccessPermission;
use MODX\Revolution\modX;
use xPDO\Transport\xPDOTransport;

if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UNINSTALL) {
  return true;
}

$modx =& $transport->xpdo;
/*$modx = new modX();
$modx->initialize('web');*/

/**
 * @param $parent id of the parent resource
 * @param $name the name of the resource
 * @param $contents the resource contents
 * @param $resGroup the group that the respurce needs permissions
 * @param $template the template that needs to be applied
 * @param $modx
 */
function createResource($parent, $name, $contents, $resGroup, $modx) {
  //first we check if the resource exists
  $resource = $modx->getObject('modResource', ['pagetitle' => $name]);

  $resource_data = array(
    'pagetitle' => $name, // The title of the new resource
    'parent' => $parent, // Assign the new resource to the parent
    'uri' => strtolower($name) . '.html',
    'template' => 0, // The ID of the template to use
    'content' => $contents,
    'published' => 1
  );

  if (empty($resource)) {
    $resource = $modx->newObject('modResource');
  }
  $resource->fromArray($resource_data);
  $resource->save();
  if (!is_null($resGroup)) {
    $resource->joinGroup($resGroup);
  }
  return $resource->get('id');
}

/** @var modX $modx */


/*$acls = ['Operator', 'ServiceManager', 'Supervisor'];

foreach ($acls as $acl) {
  $properties = array(
    'parent' => '0',
    'name'      => $acl,
    'description'      => $acl,
    'aw_users'     => '',
    'aw_resource_groups'    => '',
    'aw_parallel'    => '1',
    'aw_contexts'    => 'web',
    'policy'    => '',
    'aw_categories'    => '',
  );
  $rawResponse = $modx->runProcessor('security/group/create', $properties);
}*/

//once the acls has been created, create the resources

//First we crate the login:
$content = <<<HTML
<!DOCTYPE html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="/favicon.ico"><title>multi-code-project</title><link href="/assets/components/vlox/resourceEditor/css/app.02475123.css" rel="preload" as="style"><link href="/assets/components/vlox/resourceEditor/css/chunk-vendors.b3e5da29.css" rel="preload" as="style"><link href="/assets/components/vlox/resourceEditor/js/app.25a2f14b.js" rel="preload" as="script"><link href="/assets/components/vlox/resourceEditor/js/chunk-vendors.f60dced2.js" rel="preload" as="script"><link href="/assets/components/vlox/resourceEditor/css/chunk-vendors.b3e5da29.css" rel="stylesheet"><link href="/assets/components/vlox/resourceEditor/css/app.02475123.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but multi-code-project doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="/assets/components/vlox/resourceEditor/js/chunk-vendors.f60dced2.js"></script><script src="/assets/components/vlox/resourceEditor/js/app.25a2f14b.js"></script></body></html>
HTML;
$loginId = createResource(0, 'resEditor', $content, null, $modx);
//then we create the serviceManager
$content = <<<HTML
<!DOCTYPE html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="/favicon.ico"><title>multi-code-project</title><link href="/assets/components/vlox/vloxEditor/css/app.ecbcb3f7.css" rel="preload" as="style"><link href="/assets/components/vlox/vloxEditor/css/chunk-vendors.be14c58f.css" rel="preload" as="style"><link href="/assets/components/vlox/vloxEditor/js/app.d14b68c7.js" rel="preload" as="script"><link href="/assets/components/vlox/vloxEditor/js/chunk-vendors.659475e9.js" rel="preload" as="script"><link href="/assets/components/vlox/vloxEditor/css/chunk-vendors.be14c58f.css" rel="stylesheet"><link href="/assets/components/vlox/vloxEditor/css/app.ecbcb3f7.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but multi-code-project doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="/assets/components/vlox/vloxEditor/js/chunk-vendors.659475e9.js"></script><script src="/assets/components/vlox/vloxEditor/js/app.d14b68c7.js"></script></body></html>
HTML;
//we get the resource group
//$resource_group = $modx->getObject('modResourceGroup', ['name' => 'ServiceManager']);

createResource(0, 'vloxEditor', $content, null, $modx);


