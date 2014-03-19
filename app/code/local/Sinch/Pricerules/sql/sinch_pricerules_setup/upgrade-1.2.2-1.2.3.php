<?php
/**
 * Pricerules Upgrade Script
 * Drop unused column on Import Table
 *
 * @author Stock in the Channel
 */
$installer = $this;
$installer->startSetup();

//Remove the Unnecessary Column
$installer->getConnection()->dropColumn($installer->getTable('sinch_pricerules/import'), 'magento_customer_group_id');

$installer->endSetup();