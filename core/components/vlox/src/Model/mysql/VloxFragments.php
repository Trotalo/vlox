<?php
namespace Vlox\Model\mysql;

use xPDO\xPDO;

class VloxFragments extends \Vlox\Model\VloxFragments
{

    public static $metaMap = array (
        'package' => 'Vlox\\Model\\',
        'version' => '3.0',
        'table' => 'vlox_fragments',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'chunkName' => '',
            'title' => '',
            'description' => '',
            'properties' => NULL,
        ),
        'fieldMeta' => 
        array (
            'chunkName' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'title' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'description' => 
            array (
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => false,
                'default' => '',
            ),
            'properties' => 
            array (
                'dbtype' => 'json',
                'phptype' => 'json',
                'null' => true,
            ),
        ),
        'aggregates' => 
        array (
            'CreatedBy' => 
            array (
                'class' => 'modUser',
                'local' => 'createdby',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
            'EditedBy' => 
            array (
                'class' => 'modUser',
                'local' => 'editedby',
                'foreign' => 'id',
                'cardinality' => 'one',
                'owner' => 'foreign',
            ),
        ),
    );

}
