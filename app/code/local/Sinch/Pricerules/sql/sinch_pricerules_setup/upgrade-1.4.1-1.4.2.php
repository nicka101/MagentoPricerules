<?php
/**
 * Pricerules Upgrade Script
 * Adds the Order Column to the Group Table
 *
 * @author Stock in the Channel
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sinch_pricerules/group'),
    'execution_order',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'default'  => 4294967295,
        'comment'  => 'Execution Order'
    )
);

$installer->endSetup();