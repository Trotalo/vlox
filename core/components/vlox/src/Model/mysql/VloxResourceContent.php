<?php
namespace Vlox\Model\mysql;

use xPDO\xPDO;

class VloxResourceContent extends \Vlox\Model\VloxResourceContent
{

    public static $metaMap = array (
        'package' => 'Vlox\\Model\\',
        'version' => '3.0',
        'table' => 'vlox_resource_content',
        'tableMeta' => 
        array (
            'engine' => 'InnoDB',
        ),
        'fields' => 
        array (
            'position' => 0,
            'title' => '',
            'description' => '',
            'blockId' => 0,
            'resourceId' => 0,
            'visible' => 1,
            'properties' => NULL,
        ),
        'fieldMeta' => 
        array (
            'position' => 
            array (
                'dbtype' => 'int',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
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
            'blockId' => 
            array (
                'dbtype' => 'int',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'resourceId' => 
            array (
                'dbtype' => 'int',
                'phptype' => 'integer',
                'null' => false,
                'default' => 0,
            ),
            'visible' => 
            array (
                'dbtype' => 'boolean',
                'phptype' => 'integer',
                'null' => false,
                'default' => 1,
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
