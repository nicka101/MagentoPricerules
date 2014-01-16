<?php
/**
 * Price Rules item model
 *
 * @author Stock in the Channel
 */

class Sinch_Pricerules_Model_Pricerules extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sinch_pricerules/pricerules');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
		
        if ($this->isObjectNew()) 
		{
            $this->setData('created_at', Varien_Date::now());
        }
		
        return $this;
    }
}