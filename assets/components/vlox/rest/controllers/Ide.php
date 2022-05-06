<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */


class KrakenIde extends  modRestController {

  public function __construct(modX $modx,modRestServiceRequest $request,array $config = array()) {
    $this->modx =& $modx;
    $this->request =& $request;
    $this->config = array_merge($this->config,$config);

    $coreLocation = $this->modx->getOption('vlox.core_path', null,
      $this->modx->getOption('core_path') . 'components/vlox/');
    require_once($coreLocation . 'controllers/VloxController.php');
    require_once($coreLocation . 'controllers/VloxVueConfigurationController.php');

    VloxController::loadService($this->modx, 'VloxController');
    VloxVueConfigurationController::loadService($this->modx, 'VloxVueConfigurationController');
  }

  public function put() {
    $resContent = $this->getProperties();
    if (!empty($resContent)) {
      $resId = $resContent['id'];
      if (isset($resId) && isset($resContent['oper'])) {
        if ($resContent['oper'] === 'RUN') {
          $this->modx->VloxController->updatePackage($resId);
          $this->modx->VloxController->generateVueComponentsFiles($resId);
          $this->modx->VloxController->launchNodeServer($resId);
        } elseif ($resContent['oper'] === 'UPDATE') {
          $this->modx->VloxController->generateVueComponentsFiles($resId);
        } elseif ($resContent['oper'] === 'STOP') {
          $this->modx->VloxController->stopServer();
        } elseif ($resContent['oper'] === 'NPM_MODULE') {
          $npmResponse = $this->modx->
                VloxVueConfigurationController->modifyNpmModule($resContent['module'], $resId, $resContent['action']);
          return $this->success('success', $npmResponse);
        } elseif ($resContent['oper'] === 'BUILD') {
          $this->modx->VloxController->updatePackage($resId);
          $this->modx->VloxController->generateVueComponentsFiles($resId);
          $npmResponse = $this->modx->
          VloxVueConfigurationController->buildResource($resId);
          return $this->success('success', $npmResponse);
        }
      } elseif (isset($resContent['oper'])) {
        if($resContent['oper'] === 'SAVE_MAIN_JS') {
          $this->modx->VloxVueConfigurationController->storeMainJs($resContent['contents']);
        }
      }
    }
    return $this->success('success');
  }

  public function read($id) {
    if (!is_numeric($id)) {
      if ($id === 'RENDERER') {
        $renderer = $this->modx->getObject('modResource', array('pagetitle' => 'vloxrenderer'));
        return $this->success('Ok', $renderer->get('id'));
      } elseif ($id === 'NPM') {
        $npmModules =  $this->modx->VloxController->getNpmModules();
        return $this->success('Ok', $npmModules);
      } elseif ($id === 'NPM_LOG') {
        $npmModules =  $this->modx->VloxController->getNpmLog();
        return $this->success('Ok', $npmModules);
      } elseif (strpos($id, 'NPM_STATUS') === 0) {
        $resId = substr($id, strpos($id, '=') + 1);
        $serverStatus =  $this->modx->VloxController->getNpmStatus($resId);
        return $this->success('Ok', $serverStatus);
      } elseif ($id === 'MAIN_JS') {
        $mainJs =  $this->modx->VloxVueConfigurationController->getMainJs();
        return $this->success('Ok', $mainJs);
      }
    }
  }
}