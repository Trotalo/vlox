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

  public function put() {
    $resContent = $this->getProperties();
    echo $resContent;
    if (!empty($resContent)) {
      $coreLocation = $this->modx->getOption('vlox.core_path', null,
        $this->modx->getOption('core_path') . 'components/vlox/');
      require_once($coreLocation . 'controllers/VloxController.php');

      $resId = $resContent['id'];
      if (isset($resId) && isset($resContent['oper'])) {
        VloxController::loadService($this->modx);
        if ($resContent['oper'] === 'RUN') {
          $this->modx->VloxController->updatePackage($resId);
          $this->modx->VloxController->launchNodeServer($resId);
        } elseif ($resContent['oper'] === 'UPDATE') {
          $this->modx->VloxController->generateVueComponentsFiles($resId);
        } elseif ($resContent['oper'] === 'STOP') {
          $this->modx->VloxController->stopServer();
        }
      }
    }
    return $this->success('todo vientos!');
  }

  public function read($id) {
    if (!is_numeric($id)) {
      if ($id === 'RENDERER') {
        $renderer = $this->modx->getObject('modResource', array('pagetitle' => 'vloxrenderer'));
        return $this->success('Ok', $renderer->get('id'));
      }
    }
  }

}