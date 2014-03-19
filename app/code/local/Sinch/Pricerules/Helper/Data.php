<?php
/**
 * Price Rules Data helper
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Helper_Data extends Mage_Core_Helper_Data
{
    protected $_priceRulesItemInstance;
    protected $_priceRulesGroupItemInstance;
	
    public function getPriceRulesItemInstance()
    {
        if (!$this->_priceRulesItemInstance) 
		{
            $this->_priceRulesItemInstance = Mage::registry('pricerules_item');

            if (!$this->_priceRulesItemInstance) 
			{
                Mage::throwException($this->__('Price Rules item instance does not exist in Registry'));
            }
        }

        return $this->_priceRulesItemInstance;
    }

    public function getPriceRulesGroupItemInstance()
    {
        if (!$this->_priceRulesGroupItemInstance)
        {
            $this->_priceRulesGroupItemInstance = Mage::registry('pricerules_group_item');

            if (!$this->_priceRulesGroupItemInstance)
            {
                Mage::throwException($this->__('Price Group item instance does not exist in Registry'));
            }
        }

        return $this->_priceRulesGroupItemInstance;
    }
}