<?php
/**
 * Product List Block Override
 * Adds Manufacturer to the Select statement so it can be used in the pricerules query
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Block_Product_List extends Mage_Catalog_Block_Product_List {
	protected function _getProductCollection(){
		$coll = parent::_getProductCollection();
		$coll->addAttributeToSelect('manufacturer');
		return $coll;
	}
}