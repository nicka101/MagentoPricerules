<?php
/**
 * Pricerules Upgrade Script
 * Adds the Group Table and some constraints
 *
 * @author Stock in the Channel
 */
$installer = $this;
$installer->startSetup();

//Add the new Price Group table
$table = $installer->getConnection()
    ->newTable($installer->getTable('sinch_pricerules/group'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'primary'  => true,
        'identity' => true,
        'nullable' => false
    ), 'Identifier')
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Group ID')
    ->addColumn('group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 40, array(
        'nullable' => true,
        'default'  => ''
    ), 'Group Name')
    ->addColumn('is_manually_added', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default'  => '0'
    ), 'Manually Added')
    ->addIndex('IDX_sinch_pricerules_group_id', 'group_id', array('type' => 'UNIQUE'));
$installer->getConnection()->createTable($table);

$pricerulesTable = $installer->getTable('sinch_pricerules/pricerules');

$installer->getConnection()->changeColumn($pricerulesTable, 'customer_group_id', 'group_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable' => false,
    'unsigned' => true,
    'comment'  => 'Pricing Group'
));

//Add a Foreign Key Constraint on the pricerules table
$installer->getConnection()->addConstraint(
    'FK_sinch_pricerules_group_id',
    $pricerulesTable,
    'group_id',
    $installer->getTable('sinch_pricerules/group'),
    'group_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$importTable = $installer->getTable('sinch_pricerules/import');

//Alter the import table to the new format
$installer->getConnection()->changeColumn($importTable, 'customer_group_name', 'group_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'comment'  => 'Pricing Group'
));
//Add a Foreign Key Constraint between sinch_pricerulesimport and sinch_pricerules_groups
$installer->getConnection()->addConstraint(
    'FK_sinch_pricerules_import_group_id',
    $importTable,
    'group_id',
    $installer->getTable('sinch_pricerules/group'),
    'group_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$installer->endSetup();