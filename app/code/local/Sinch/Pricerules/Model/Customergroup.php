<?php

class Sinch_Pricerules_Model_Customergroup
{
	public function getName($id)
	{
		return Mage::getSingleton('customer/group')->load($id)->getCode();
	}
}