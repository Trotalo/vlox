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
    return $resContent;
  }

}