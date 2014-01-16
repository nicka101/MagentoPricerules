<?php
class Sinch_Pricerules_Block_Product_List extends Mage_Catalog_Block_Product_List {
	public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = ''){
		$this->setTemplate('catalog/product/price.phtml');
		$this->setProduct($product);
		//Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => 1));
		return $this->toHtml();
	}
	
	protected function _getProductCollection(){
		$coll = parent::_getProductCollection();
		$coll->addAttributeToSelect('manufacturer');
		return $coll;
	}
}