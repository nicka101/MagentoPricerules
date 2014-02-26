<?php
/**
 * Data Upgrade Script
 * Fixes IS NULL checks in existing rules
 *
 * @author Stock in the Channel
 */

$pricerules = Mage::getModel('sinch_pricerules/pricerules')->getCollection();

foreach($pricerules as $rule){
	$changed = false;
	if($rule->getCategoryId() == 0){
		$changed = true;
		$rule->setCategoryId(null);
	}
	if($rule->getBrandId() == 0){
		$changed = true;
		$rule->setBrandId(null);
	}
	if($changed) $rule->save();
}