<?php
/**
 * Pricerules Upgrade Script
 * Removes Foreign Key on customer_group
 *
 * @author Stock in the Channel
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
	$installer->getTable("sinch_pricerules/pricerules"),
	"FK_sinch_pricerules_customer_group"
);

$installer->endSetup();