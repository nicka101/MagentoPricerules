<?php
/**
 * V1 API Model
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Model_Api extends Mage_Api_Model_Resource_Abstract
{
	public function setpricerulesgroup($customerId, $groupId){
		$customer = Mage::getModel('customer/customer')->load($customerId);
		if($customer === null) return false;
		$customer->setSinchPricerulesGroup($groupId);
		$customer->save();
		return true;
	}
}