<?php
/**
 * Upgrade Script for 1.2.3 to 1.3.1
 * Large version change as this adds a table (and change the table engine for the brands table)
 * I would also do the same to the import table but only InnoDB respects Foreign Keys and data integrity is more important in this case
 *
 * @author Stock in the Channel
 */

$installer = $this;
$installer->startSetup();

$newTable = $installer->getConnection()
    ->newTable($installer->getTable('sinch_pricerules/brand'))
    ->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true
    ), 'Brand ID')
    ->addColumn('brand_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Brand Name')
    ->addIndex('IDX_sinch_pricerules_brand_name', 'brand_name');
$installer->getConnection()->createTable($newTable);

$installer->getConnection()->changeTableEngine($installer->getTable('sinch_pricerules/brand'), 'MEMORY');

$installer->endSetup();