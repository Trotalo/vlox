<?php
/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

$coreLocation = $modx->getOption('vlox.core_path', null,
                            $modx->getOption('core_path') . 'components/vlox/');

require_once($coreLocation . 'controllers/utils/SchemaGenerator.php');

$loadTable = new SchemaGenerator();
$path = MODX_BASE_PATH . 'vlox/core/components/vlox/model/schema/';
$loadTable->addNewTable($path);