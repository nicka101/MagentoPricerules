<?php
/**
 * Pricerules Upgrade Script
 * Makes the Customer Attribute a multi-select
 *
 * @author Stock in the Channel
 */

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('sinch_pricerules_setup');
$setup->startSetup();

$spgAttributeId = $setup->getAttribute($customerAttributeEntityType, 'sinch_pricerules_group', 'attribute_id');
$setup->updateAttribute($customerAttributeEntityType, $spgAttributeId, array(
    'frontend_input' => 'multiselect',
    'backend_model'  => 'eav/entity_attribute_backend_array',
    'backend_type'   => 'varchar'
));

$setup->endSetup();

$installer->endSetup();