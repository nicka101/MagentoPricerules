<?php
/**
 * Price Rule List block
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Block_List extends Mage_Core_Block_Template
{
    protected $_priceRulesCollection = null;

    protected function _getCollection()
    {
        return  Mage::getResourceModel('sinch_pricerules/pricerules_collection');
    }

    public function getCollection()
    {
        if (is_null($this->_priceRulesCollection)) 
		{
            $this->_priceRulesCollection = $this->_getCollection();
            $this->_priceRulesCollection->prepareForList($this->getCurrentPage());
        }

        return $this->_priceRulesCollection;
    }

    public function getItemUrl($priceRulesItem)
    {
        return $this->getUrl('*/*/view', array('id' => $priceRulesItem->getId()));
    }
	
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
	
    public function getPager()
    {
        $pager = $this->getChild('pricerules_list_pager');
		
        if ($pager) 
		{
            $priceRulesPerPage = Mage::helper('sinch_pricerules')->getPriceRulesPerPage();

            $pager->setAvailableLimit(array($priceRulesPerPage => $priceRulesPerPage));
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(true);

            return $pager->toHtml();
        }

        return null;
    }
}
