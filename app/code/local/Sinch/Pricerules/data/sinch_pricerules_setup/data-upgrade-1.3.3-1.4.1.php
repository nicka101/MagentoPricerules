<?php
/**
 * Data Upgrade Script 1.3.3-1.4.1
 *
 * @author Stock in the Channel
 */

$dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');

$dbWrite->query("INSERT INTO " . Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar') . " (entity_type_id, attribute_id, entity_id, value)
SELECT entity_type_id, attribute_id, entity_id, value
FROM " . Mage::getSingleton('core/resource')->getTableName('customer_entity_int') . "
WHERE attribute_id = (SELECT attribute_id FROM " . Mage::getSingleton('core/resource')->getTableName('eav/attribute') . " WHERE attribute_code = 'sinch_pricerules_group' LIMIT 1)");

$dbWrite->query("DELETE FROM " . Mage::getSingleton('core/resource')->getTableName('customer_entity_int') . "
WHERE attribute_id = (SELECT attribute_id FROM " . Mage::getSingleton('core/resource')->getTableName('eav/attribute') . " WHERE attribute_code = 'sinch_pricerules_group' LIMIT 1)");