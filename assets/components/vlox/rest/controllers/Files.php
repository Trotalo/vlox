<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

class VloxFiles extends  \MODX\Revolution\Rest\modRestController {
  /** @var string $classKey The xPDO class to use */
  public $classKey = 'none';
  /** @var string $defaultSortField The default field to sort by in the getList method */
  public $defaultSortField = 'id';
  /** @var string $defaultSortDirection The default direction to sort in the getList method */
  public $defaultSortDirection = 'ASC';

  public function __construct(modX $modx,modRestServiceRequest $request,array $config = array()){
    parent::__construct($modx, $request, $config);
  }

  public function post() {
    $fileName = round(microtime(true) * 1000) . '-' .basename($_FILES['file']['name']);
    $target = $this->modx->config['base_path'] . "assets/media/" . $fileName;
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target)) {
      echo $fileName;
    } else {
      echo "failed";
    }
  }

}