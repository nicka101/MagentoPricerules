<?php
/**
 * Pricerules Upgrade Script
 * Makes the Customer Attribute a Select
 *
 * @author Stock in the Channel
 */
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('sinch_pricerules_setup');
$setup->startSetup();

$customerAttributeEntityType = $setup->getEntityTypeId('customer');

$spgAttributeId = $setup->getAttribute($customerAttributeEntityType, 'sinch_pricerules_group', 'attribute_id');
$setup->updateAttribute($customerAttributeEntityType, $spgAttributeId, array(
    'frontend_input' => 'select',
    'source_model' => 'sinch_pricerules/attribute_source_group'
));

$setup->endSetup();
$installer->endSetup();