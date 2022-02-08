<?php
$coreLocation = $modx->getOption('vlox.core_path', null,
                            $modx->getOption('core_path') . 'components/vlox/');

require_once($coreLocation . 'controllers/utils/SchemaGenerator.php');

$loadTable = new SchemaGenerator();
$path = MODX_BASE_PATH . 'vlox/core/components/vlox/model/schema/';
$loadTable->addNewTable($path);