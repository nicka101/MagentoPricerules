<?php
/**
 * Price Rules collection
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Model_Resource_Pricerules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sinch_pricerules/pricerules');
    }

    public function prepareForList($page)
    {
        $this->setPageSize(Mage::helper('sinch_pricerules')->getPriceRulesPerPage());
        $this->setCurPage($page)->setOrder('pricerules_id', Varien_Data_Collection::SORT_ORDER_DESC);
        return $this;
    }
}