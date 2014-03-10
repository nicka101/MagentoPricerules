<?php
/*
 * Pricerules Upgrade Script
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey($installer->getTable('eav/attribute_option_value'), 'IDX_eav_attribute_option_value_value','value');

$installer->endSetup();