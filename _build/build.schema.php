<?php

require_once('./mysqlTable.php');

$loadTable = new MysqlTable();
$path = KRAKEN_PATH . '/modelConfig/';
$loadTable->addNewTable($path);
