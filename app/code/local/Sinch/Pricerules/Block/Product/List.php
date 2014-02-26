<?php
class Sinch_Pricerules_Block_Product_List extends Mage_Catalog_Block_Product_List {
	protected function _getProductCollection(){
		$coll = parent::_getProductCollection();
		$coll->addAttributeToSelect('manufacturer');
		return $coll;
	}
}