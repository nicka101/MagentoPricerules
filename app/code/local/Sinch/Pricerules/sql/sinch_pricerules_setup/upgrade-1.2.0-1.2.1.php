<?php
/*
 * Pricerules Upgrade Script
 *
 * Removes Customer Group Foreign Key
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey(
	$installer->getTable("sinch_pricerules/pricerules"),
	"FK_sinch_pricerules_customer_group"
);

$installer->endSetup();