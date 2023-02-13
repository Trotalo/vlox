<?php
/**
 * Resolve Resources
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package vlox
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if (!$object->xpdo) return false;
/** @var modX $modx */
$modx =& $object->xpdo;

if (!function_exists('getResourceMap')) {
    function getResourceMap($modx) {
        //global $modx;

        $assetsPath = rtrim($modx->getOption('vlox.assets_path',null,$modx->getOption('assets_path').'components/vlox/'), '/') . '/';
        $rmf = $assetsPath . 'resourcemap.php';

        if (is_readable($rmf)) {
            $map = include $rmf;
        } else {
            $map = array();
        }

        return $map;
    }
}

if (!function_exists('setResourceMap')) {
    function setResourceMap($modx,$resourceMap) {
        //global $modx;

        $assetsPath = rtrim($modx->getOption('vlox.assets_path',null,$modx->getOption('assets_path').'components/vlox/'), '/') . '/';
        $rmf = $assetsPath . 'resourcemap.php';
        file_put_contents($rmf, '<?php return ' . var_export($resourceMap, true) . ';');

    }
}

if (!function_exists('createResource')) {
    function createResource($modx,$resource) {
        //global $modx;

        if (isset($resource['tvs'])) {
            $tvs = $resource['tvs'];
            unset($resource['tvs']);
        } else {
            $tvs = array();
        }
        
        if (isset($resource['others'])) {
            $others = $resource['others'];
            unset($resource['others']);

            $taggerCorePath = $modx->getOption('tagger.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tagger/');
            if (file_exists($taggerCorePath . 'model/tagger/tagger.class.php')) {
                /** @var Tagger $tagger */
                $tagger = $modx->getService(
                    'tagger',
                    'Tagger',
                    $taggerCorePath . 'model/tagger/',
                    array(
                        'core_path' => $taggerCorePath
                    )
                );
            
                $tagger = $tagger instanceof Tagger;
            } else {
                $tagger = null;
            }
            
            foreach ($others as $other) {
                if (($tagger == true) && (strpos($other['name'], 'tagger-') !== false)) {
                    $groupAlias = preg_replace('/tagger-/', '', $other['name'], 1);
            
                    $group = $modx->getObject('TaggerGroup', array('alias' => $groupAlias));
                    if ($group) {
                        $other['name'] = 'tagger-' . $group->id;
                    }
                }
            
                $resource[$other['name']] = $other['value'];
            }
        }

        $res = $modx->runProcessor('resource/create', $resource);
        $resObject = $res->getObject();

        if ($resObject && isset($resObject['id'])) {
            /** @var modResource $modResource */
            $modResource = $modx->getObject('modResource', array('id' => $resObject['id']));

            if ($modResource) {
                foreach ($tvs as $tv) {
                    $modResource->setTVValue($tv['name'], $tv['value']);
                }

                return $modResource->id;
            }
        }

        return false;
    }
}

if (!function_exists('updateResource')) {
    function updateResource($modx,$resource) {
        //global $modx;

        if (isset($resource['tvs'])) {
            $tvs = $resource['tvs'];
            unset($resource['tvs']);
        } else {
            $tvs = array();
        }

        if (isset($resource['others'])) {
            $others = $resource['others'];
            unset($resource['others']);

            $taggerCorePath = $modx->getOption('tagger.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tagger/');
            if (file_exists($taggerCorePath . 'model/tagger/tagger.class.php')) {
                /** @var Tagger $tagger */
                $tagger = $modx->getService(
                    'tagger',
                    'Tagger',
                    $taggerCorePath . 'model/tagger/',
                    array(
                        'core_path' => $taggerCorePath
                    )
                );
            
                $tagger = $tagger instanceof Tagger;
            } else {
                $tagger = null;
            }

            foreach ($others as $other) {
                if (($tagger == true) && (strpos($other['name'], 'tagger-') !== false)) {
                    $groupAlias = preg_replace('/tagger-/', '', $other['name'], 1);
                
                    $group = $modx->getObject('TaggerGroup', array('alias' => $groupAlias));
                    if ($group) {
                        $other['name'] = 'tagger-' . $group->id;
                    }
                }

                $resource[$other['name']] = $other['value'];
            }
        }

        $res = $modx->runProcessor('resource/update', $resource);
        $resObject = $res->getObject();

        if ($resObject && isset($resObject['id'])) {
            /** @var modResource $modResource */
            $modResource = $modx->getObject('modResource', array('id' => $resObject['id']));

            if ($modResource) {
                foreach ($tvs as $tv) {
                    $modResource->setTVValue($tv['name'], $tv['value']);
                }

                return $modResource->id;
            }
        }

        return false;
    }
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $resources = array (
  0 => 
  array (
    'pagetitle' => 'resEditor',
    'alias' => 'resEditor',
    'parent' => 0,
    'content' => '<!DOCTYPE html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="/favicon.ico"><title>multi-code-project</title><link href="/assets/components/vlox/resourceEditor/css/app.11981478.css" rel="preload" as="style"><link href="/assets/components/vlox/resourceEditor/css/chunk-vendors.b3e5da29.css" rel="preload" as="style"><link href="/assets/components/vlox/resourceEditor/js/app.f40b54f4.js" rel="preload" as="script"><link href="/assets/components/vlox/resourceEditor/js/chunk-vendors.f60dced2.js" rel="preload" as="script"><link href="/assets/components/vlox/resourceEditor/css/chunk-vendors.b3e5da29.css" rel="stylesheet"><link href="/assets/components/vlox/resourceEditor/css/app.11981478.css" rel="stylesheet"></head><body><noscript><strong>We\'re sorry but multi-code-project doesn\'t work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="/assets/components/vlox/resourceEditor/js/chunk-vendors.f60dced2.js"></script><script src="/assets/components/vlox/resourceEditor/js/app.f40b54f4.js"></script></body></html>',
    'context_key' => 'web',
    'class_key' => 'modDocument',
    'longtitle' => '',
    'description' => '',
    'isfolder' => 0,
    'introtext' => '',
    'deleted' => 0,
    'menutitle' => '',
    'hide_children_in_tree' => 0,
    'show_in_tree' => 1,
    'set_as_home' => 0,
    'tvs' => 
    array (
    ),
    'others' => 
    array (
    ),
    'link_attributes' => '',
    'template' => 0,
  ),
  1 => 
  array (
    'pagetitle' => 'vloxEditor',
    'alias' => 'vloxEditor',
    'parent' => 0,
    'content' => '<!DOCTYPE html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="/favicon.ico"><title>multi-code-project</title><link href="/assets/components/vlox/vloxEditor/css/app.4b71b78d.css" rel="preload" as="style"><link href="/assets/components/vlox/vloxEditor/css/chunk-vendors.be14c58f.css" rel="preload" as="style"><link href="/assets/components/vlox/vloxEditor/js/app.a5e86cd6.js" rel="preload" as="script"><link href="/assets/components/vlox/vloxEditor/js/chunk-vendors.50f24ffc.js" rel="preload" as="script"><link href="/assets/components/vlox/vloxEditor/css/chunk-vendors.be14c58f.css" rel="stylesheet"><link href="/assets/components/vlox/vloxEditor/css/app.4b71b78d.css" rel="stylesheet"></head><body><noscript><strong>We\'re sorry but multi-code-project doesn\'t work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="/assets/components/vlox/vloxEditor/js/chunk-vendors.50f24ffc.js"></script><script src="/assets/components/vlox/vloxEditor/js/app.a5e86cd6.js"></script></body></html>',
    'context_key' => 'web',
    'class_key' => 'modDocument',
    'longtitle' => '',
    'description' => '',
    'isfolder' => 0,
    'introtext' => '',
    'deleted' => 0,
    'menutitle' => '',
    'hide_children_in_tree' => 0,
    'show_in_tree' => 1,
    'set_as_home' => 0,
    'tvs' => 
    array (
    ),
    'others' => 
    array (
    ),
    'link_attributes' => '',
    'template' => 0,
  ),
  2 => 
  array (
    'pagetitle' => 'vloxrenderer',
    'alias' => 'vloxrenderer',
    'parent' => 0,
    'content' => '',
    'context_key' => 'web',
    'class_key' => 'modDocument',
    'longtitle' => '',
    'description' => '',
    'isfolder' => 0,
    'introtext' => '',
    'deleted' => 0,
    'menutitle' => '',
    'hide_children_in_tree' => 0,
    'show_in_tree' => 1,
    'set_as_home' => 0,
    'tvs' => 
    array (
    ),
    'others' => 
    array (
    ),
    'link_attributes' => '',
    'template' => 0,
  ),
);

        if (isset($options['install_resources']) && empty($options['install_resources'])) return true;

        $resourceMap = getResourceMap($modx);
        $toRemove = $resourceMap;
        $siteStart = $modx->getOption('site_start');

        foreach ($resources as $resource) {
            if (is_string($resource['parent'])) {
                if (isset($resourceMap[$resource['parent']])) {
                    $resource['parent'] = $resourceMap[$resource['parent']];
                } else {
                    /** @var modResource $parent */
                    $parent = $modx->getObject('modResource', array('pagetitle' => $resource['parent']));
                    if ($parent) {
                        $resource['parent'] = $parent->id;
                    }
                }
            } else {
                if ($resource['parent'] != 0) {
                    /** @var modResource $parent */
                    $parent = $modx->getObject('modResource', array('id' => $resource['parent']));
                    if ($parent) {
                        $resource['parent'] = $parent->id;
                    }
                } else {
                    $resource['parent'] = 0;
                }
            }

            if ($resource['template'] !== null) {
                if ($resource['template'] !== 0) {
                    $template = $modx->getObject('modTemplate', array('templatename' => $resource['template']));
                    if ($template) {
                        $resource['template'] = $template->id;
                    }
                } else {
                    $resource['template'] = 0;
                }
            }

            if ($resource['content_type'] !== null) {
                $content_type = $modx->getObject('modContentType', array('name' => $resource['content_type']));
                if ($content_type) {
                    $resource['content_type'] = $content_type->id;
                }
            } else {
                $resource['content_type'] = $modx->getOption('default_content_type', null, 1);
            }

            if (isset($resourceMap[$resource['pagetitle']])) {
                unset($toRemove[$resource['pagetitle']]);

                /** @var modResource $exists */
                $exists = $modx->getObject('modResource', array('id' => $resourceMap[$resource['pagetitle']]));
                if ($exists) {
                    $resource['id'] = $exists->id;
                    $resId = updateResource($modx,$resource);

                    if ($resId !== false) {
                        $resourceMap[$resource['pagetitle']] = $resId;
                    }
                } else {
                    if ($resource['set_as_home'] == 1) {
                        unset($resource['set_as_home']);
                        $resource['id'] = $siteStart;

                        $resId = updateResource($modx,$resource);

                        if ($resId !== false) {
                            $resourceMap[$resource['pagetitle']] = $resId;
                        }
                    } else {
                        $resId = createResource($modx,$resource);

                        if ($resId !== false) {
                            $resourceMap[$resource['pagetitle']] = $resId;
                        }
                    }
                }
            } else {
                if ($resource['set_as_home'] == 1) {
                    unset($resource['set_as_home']);
                    $resource['id'] = $siteStart;
                
                    $resId = updateResource($modx,$resource);
                
                    if ($resId !== false) {
                        $resourceMap[$resource['pagetitle']] = $resId;
                    }
                } else {
                    $resId = createResource($modx,$resource);

                    if ($resId !== false) {
                        $resourceMap[$resource['pagetitle']] = $resId;
                    }
                }
            }
        }

        foreach ($toRemove as $pageTitle => $resource) {
            unset($resourceMap[$pageTitle]);

            if ($resource == $siteStart) continue;

            /** @var modResource $modResource */
            $modResource = $modx->getObject('modResource', $resource);
            if ($modResource) {
                $modx->updateCollection('modResource', array('parent' => 0), array('parent' => $resource));

                $modResource->remove();
            }
        }

        setResourceMap($modx,$resourceMap);

        break;
    case xPDOTransport::ACTION_UNINSTALL:

        break;
}

return true;