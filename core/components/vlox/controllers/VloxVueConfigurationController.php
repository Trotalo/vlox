<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

require_once MODX_BASE_PATH . 'config.core.php';
require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

require_once 'VloxBaseController.php';

class VloxVueConfigurationController extends  VloxBaseController {


  public function storeMainJs($fileContents) {
    $chunk = $this->modx->getObject('modChunk', array('name'=>'mainJs'));
    $chunk->set('snippet', $fileContents);
    $chunk->save();
  }
}