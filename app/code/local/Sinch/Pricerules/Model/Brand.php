<?php
/**
 * Brand Model
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Model_Brand
{
	public function getName($id)
	{
		$attr = Mage::getModel('eav/entity_attribute_option')
			->getCollection()
			->setStoreFilter()
			->join('attribute','attribute.attribute_id = main_table.attribute_id', 'attribute_code')
			->addFieldToFilter('main_table.option_id',array('eq' => $id))->getFirstItem(); 

		return ($attr->value);
	}
	
	public function getOptionArray($attributeCode) 
	{
		$product = Mage::getSingleton('catalog/product');
		$attribute = $product->getResource()->getAttribute($attributeCode);
        
		if ($attribute)
		{
			$frontendAttributes = $attribute->getFrontend();
			$attributes = $frontendAttributes->getSelectOptions();
		
			$options = array();
			$options[0] = "-- Please Select --";
		
			foreach($attributes as $attribute) 
			{
				if ($attribute['value']) 
				{
					$options[$attribute['value']] = $attribute['label'];
				}
			}
		}
		return $options;
	}
}