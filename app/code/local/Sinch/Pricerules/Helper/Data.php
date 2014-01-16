<?php
/**
 * Price Rules Data helper
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_pricerulesItemInstance;
	
    public function getPriceRulesItemInstance()
    {
        if (!$this->_priceRulesItemInstance) 
		{
            $this->_priceRulesItemInstance = Mage::registry('pricerules_item');

            if (!$this->_priceRulesItemInstance) 
			{
                Mage::throwException($this->__('Price rules item instance does not exist in Registry'));
            }
        }

        return $this->_priceRulesItemInstance;
    }
}